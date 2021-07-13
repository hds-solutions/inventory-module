<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Interfaces\Document;
use HDSSolutions\Finpar\Traits\HasDocumentActions;
use HDSSolutions\Finpar\Traits\HasPartnerable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;
use Staudenmeir\EloquentHasManyDeep\HasRelationships as HasExtendedRelationships;

class InOut extends X_InOut implements Document {
    use HasDocumentActions,
        HasPartnerable;

    use HasExtendedRelationships;

    public static function nextDocumentNumber():string {
        // return next document number for specified stamping
        return str_increment(self::max('document_number') ?? null);
    }

    public function __construct(array|Order|Invoice $attributes = []) {
        // check if is instance of Order
        if (($order = $attributes) instanceof Order) $attributes = self::fromResource($order, 'order_id');
        // check if is instance of Invoice
        if (($order = $attributes) instanceof Invoice) $attributes = self::fromResource($order, 'invoice_id');
        // redirect attributes to parent
        parent::__construct(is_array($attributes) ? $attributes : []);
    }

    private static function fromResource(Order|Invoice $resource, string $relation):array {
        // copy attributes from resource
        return [
            'branch_id'         => $resource->branch_id,
            'warehouse_id'      => $resource->warehouse_id,
            'employee_id'       => $resource->employee_id,
            'partnerable_type'  => $resource->partnerable_type,
            'partnerable_id'    => $resource->partnerable_id,
            $relation           => $resource->id,
            'transacted_at'     => $resource->transacted_at,
            'is_purchase'       => $resource->is_purchase,
        ];
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function orders() {
        return $this->hasManyDeep(Order::class, [
            InOutLine::class,   InOutLineInvoiceLine::class,
            InvoiceLine::class, InvoiceLineOrderLine::class,
            OrderLine::class,
        ], [
            'deleted_at',       // bypass (idk why adds an IS NULL comparison with this column)
            'in_out_line_id',   // see (2) below
            'id',               // see (3) below
            'invoice_line_id',  // see (4) below
            'id',               // see (5) below
            'id',               // see (6) below
        ], [
            '??',               // ignored (not used in query)
            'id',               // (2) InOutLine.id = InOutLineInvoiceLine.in_out_line_id
            'invoice_line_id',  // (3) InOutLineInvoiceLine.invoice_line_id = InvoiceLine.id
            'id',               // (4) InvoiceLine.id = InvoiceLineOrderLine.invoice_line_id
            'order_line_id',    // (5) InvoiceLineOrderLine.order_line_id = OrderLine.id
            'order_id',         // (6) OrderLine.order_id = Order.id
        // prevent columns overlap
        ])->select('orders.*');
    }

    public function lines() {
        return $this->hasMany(InOutLine::class);
    }

    public function hasProduct(int|Product $product, int|Variant|null $variant = null) {
        // get order lines
        $lines = $this->lines();

        // filter product
        $lines->where('product_id', $product instanceof Product ? $product->id : $product);
        // filter variant if specified
        if ($variant !== null) $lines->where('variant_id', $variant instanceof Variant ? $variant->id : $variant);
        else $lines->whereNull('variant_id');

        // return if there is lines with specified product|variant
        return $lines->count() > 0;
    }

    public function beforeSave(Validator $validator) {
        // TODO: set employee from session
        if (!$this->exists && $this->employee_id === null) $this->employee()->associate( auth()->user() );

        // if document is material return and invoice not set
        if ($this->is_material_return && $this->invoice === null)
            // reject it, Invoice must be specified when returning
            $validator->errors()->add('invoice_id', __('inventory::inout.material-return-invoice'));

        // check if new record and no document number is set
        if (!$this->exists && !$this->document_number)
            // set document number incrementing by 10
            $this->document_number = self::nextDocumentNumber();
    }

    public function prepareIt():?string {
        // validations when is material_return
        if ($this->is_material_return) {

            // get orders through far orders relationship (see this.orders() method)
            foreach ($this->orders()->get() as $order)
                // InOut's of Order must be completed
                if (self::ofOrder( $order )->open()->count())
                    // return process error
                    return $this->documentError('inventory::in_out.order-has-pending-inouts', [
                        'order' => $this->order,
                    ]);

            // check that lines has qty movement and invoiced aty
            foreach ($this->lines as $line) {
                // check that line movement quantity isn't 0 (zero)
                if ($line->quantity_movement === 0)
                    // reject with process error
                    return $this->documentError('inventory::in_out.lines.qty-zero', [
                        'product'   => $line->product->name,
                        'variant'   => $line->variant?->sku,
                    ]);

                // TODO: check that qty <= invoiced
                $already_returned = $quantity_invoiced = 0;
                foreach ($this->invoice->materialReturns->pluck('lines')->flatten() as $returnedLine) {
                    // check if line matches with invoiceLine
                    if ($returnedLine->invoice_line_id !== $line->invoice_line_id) continue;
                    // add already returned quantity for current line
                    $already_returned += $returnedLine->quantity_movement;
                    // save invoiced quantity
                    $quantity_invoiced = $returnedLine->invoiceLine->quantity_invoiced;
                }

                // check if returning quantity > quantity available to return
                if ($line->quantity_movement > ($available = $quantity_invoiced - $already_returned))
                    // reject with error
                    return $this->documentError('inventory::in_out.lines.returning-gt-available', [
                        'product'   => $line->product->name,
                        'variant'   => $line->variant?->sku,
                        'available' => $available,
                    ]);
            }
        }

        // return status InProgress
        return Document::STATUS_InProgress;
    }

    public function completeIt():?string {
        // process lines, updating stock based on document type
        foreach ($this->lines as $line) {
            // save total quantity to move
            $quantityToMove = $line->quantity_movement;

            // get Variant|Product locators
            foreach (($line->variant ?? $line->product)->locators as $locator) {
                // update storage
                if (!$this->updateStorage(Storage::getFromProductOnLocator($line->product, $line->variant, $locator), $quantityToMove))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already moved and exit loop
                if ($quantityToMove == 0) break;
            }

            // update stock for Variant|Product on existing Storages
            foreach (Storage::getFromProduct($line->product, $line->variant, $this->branch) as $storage) {
                // update existing storage
                if (!$this->updateStorage($storage, $quantityToMove))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already moved and exit loop
                if ($quantityToMove == 0) break;
            }

            // if not all movement quantity can be moved, reject process
            if ($quantityToMove > 0)
                // return document error
                return $this->documentError('inventory::in_out.lines.'.($this->is_material_return ? 'no-storage-found' : 'no-stock'), [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]);

            // update delivered/received quantity on Order/Invoice
            if (!$this->is_material_return) {
                // if is_sale, update OrderLine.quantity_delivered
                if ($this->is_sale && !$line->orderLine->update([ 'quantity_delivered' => $line->quantity_movement ]))
                    // return document error
                    return $this->documentError( $line->orderLine->errors()->first() );

                // if is_purchase, update InvoiceLine.quantity_received
                if ($this->is_purchase && !$line->invoiceLine->update([ 'quantity_received' => $line->quantity_movement ]))
                    // return document error
                    return $this->documentError( $line->invoiceLine->errors()->first() );
            }
        }

        // if document is material_return, create a CreditNote for the returning amount
        if ($this->is_material_return) {
            // create CreditNote
            if (!($creditNote = CreditNote::createFromMaterialReturn( $this ))->exists || $creditNote->errors()->count() > 0)
                // redirect creditNote error
                return $this->documentError( $creditNote->errors()->first() );
        }

        // return completed status
        return Document::STATUS_Completed;
    }

    private function updateStorage(Storage $storage, int &$quantityToMove):bool {
        // if document is_material_return, add returned stock on Storage.onhand
        if ($this->is_material_return) {
            // update stock on storage
            $storage->fill([
                // add movement quantity to storage.onHand
                'onhand'    => $storage->onhand + $quantityToMove,
            ]);
            // set quantity to move to 0 (zero), all movement when to first location found
            $quantityToMove = 0;

        } else {
            // if document is_sale, substract stock from Storage
            if ($this->is_sale) {
                // get available onhand stock on current storage
                $available = $storage->reserved > $quantityToMove ? $quantityToMove : $storage->available;
                // update stock on storage
                $storage->fill([
                    // substract available from storage.onHand
                    'onhand'    => $storage->onhand - $available,
                    // substract available from storage.reserved
                    'reserved'  => $storage->reserved - $available,
                ]);

                // substract available from total quantity to move
                $quantityToMove -= $available;
            }

            // if document is_purchase, add available stock on Storage
            if ($this->is_purchase) {
                // get available pending stock on current storage
                $received = $storage->pending > $quantityToMove ? $quantityToMove : $storage->pending;
                // update stock on storage
                $storage->fill([
                    // add movement quantity to storage.onHand
                    'onhand'    => $storage->onhand + $received,
                    // substract movement quantity from storage.pending
                    'pending'   => $storage->pending - $received,
                ]);

                // set quantity to move to 0 (zero), all movement when to first location found
                $quantityToMove -= $received;
            }
        }

        // save storage changes, and document error if failed
        return !$storage->save() ? $this->documentError( $storage->errors()->first() ) : true;
    }

    public function scopeOfOrder(Builder $query, int|Order $order) {
        // return InOut's from order
        return $query->where('order_id', $order instanceof Order ? $order->id : $order);
    }

    public static function createFromOrder(int|Order $order, array $attributes = []):self {
        // make InOut resource
        $resource = self::makeFromOrder($order, $attributes);

        // stop process if inOut can't be saved
        if (!$resource->save())
            // return error through document error
            return tap($resource, fn($resource) => $resource->documentError( $resource->errors()->first() ));

        // foreach lines
        foreach ($resource->lines as $line) {
            // link with parent
            $line->inOut()->associate($resource);
            // stop process if line can't be saved
            if (!$line->save())
                // return error through document error
                return tap($resource, fn($resource) => $resource->documentError( $line->errors()->first() ));
        }

        // return created inOut resource
        return $resource;
    }

    public static function makeFromOrder(int|Order $order, array $attributes = []):self {
        // load order if isn't instance
        if (!$order instanceof Order) $order = Order::findOrFail($order);

        // create new resource from Order
        $resource = new self($order);
        // append extra attributes
        $resource->fill( $attributes );

        // create InvoiceLines from OrderLines
        $order->lines->each(function($orderLine) use ($resource) {
            // ignore line if product.type isn't stockable
            if (!$orderLine->product->stockable) return;
            // create a new InvoiceLine from OrderLine
            $resource->lines->push( $line = $resource->lines()->make($orderLine) );
            // set first locator of Product|Variant
            $line->locator()->associate( ($orderLine->variant ?? $orderLine->product)->locators()->first() );
        });

        // return resource
        return $resource;
    }

    public static function createFromInvoice(int|Invoice $invoice, array $attributes = []):self {
        // make InOut resource
        $resource = self::makeFromInvoice($invoice, $attributes);

        // stop process if inOut can't be saved
        if (!$resource->save())
            // return error through document error
            return tap($resource, fn($resource) => $resource->documentError( $resource->errors()->first() ));

        // foreach lines
        foreach ($resource->lines as $line) {
            // link with parent
            $line->inOut()->associate($resource);
            // stop process if line can't be saved
            if (!$line->save())
                // return error through document error
                return tap($resource, fn($resource) => $resource->documentError( $line->errors()->first() ));
        }

        // return created inOut resource
        return $resource;
    }

    public static function makeFromInvoice(int|Invoice $invoice, array $attributes = []):self {
        // load invoice if isn't instance
        if (!$invoice instanceof Invoice) $invoice = Invoice::findOrFail($invoice);

        // create new resource from Invoice
        $resource = new self($invoice);
        // append extra attributes
        $resource->fill( $attributes );

        // create InvoiceLines from OrderLines
        $invoice->lines->each(function($invoiceLine) use ($resource) {
            // ignore line if product.type isn't stockable
            if (!$invoiceLine->product->stockable) return;
            // create a new InvoiceLine from OrderLine
            $resource->lines->push( $line = $resource->lines()->make($invoiceLine) );
            // set first locator of Product|Variant
            $line->locator()->associate( ($invoiceLine->variant ?? $invoiceLine->product)->locators()->first() );
        });

        // return resource
        return $resource;
    }

}

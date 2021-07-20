<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Interfaces\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class MaterialReturn extends A_InOut {

    protected $table = 'in_outs';

    public function getForeignKey() {
        return Str::snake(class_basename(InOut::class)).'_'.$this->getKeyName();
    }

    public function __construct(array|Invoice $attributes = []) {
        // check if is instance of Invoice
        if (($order = $attributes) instanceof Invoice) $attributes = self::fromResource($order, 'invoice_id');
        // redirect attributes to parent
        parent::__construct((is_array($attributes) ? $attributes : []) + [
            // force purchase=false && material_return=true
            'is_purchase'           => false,
            'is_material_return'    => true,
        ]);
    }

    protected static function booted() {
        self::addGlobalScope('material_return', fn(Builder $query) => $query->where('is_material_return', true));
    }

    public function setIsPurchaseAttribute(bool $ignored):void {
        $this->attributes['is_purchase'] = false;
    }

    public function setIsMaterialReturnAttribute(bool $ignored):void {
        $this->attributes['is_material_return'] = true;
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function creditNote() {
        return $this->morphOne(CreditNote::class, 'documentable');
    }

    public function lines() {
        return $this->hasMany(MaterialReturnLine::class);
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

    public function prepareIt():?string {
        // get orders through far orders relationship (see this.orders() method)
        foreach ($this->orders()->get() as $order)
            // InOut's of Order must be completed
            if (InOut::ofOrder( $order )->open()->count())
                // return process error
                return $this->documentError('inventory::material_return.order-has-pending-in_outs', [
                    'order' => $this->order,
                ]);

        // check that lines has qty movement and invoiced aty
        foreach ($this->lines as $line) {
            // load invoiced quantity
            $quantity_invoiced = $line->invoiceLine->quantity_invoiced;
            // check that line movement quantity isn't 0 (zero)
            if ($line->quantity_movement === 0)
                // reject with process error
                return $this->documentError('inventory::material_return.lines.qty-zero', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]);

            // check that qty to return <= invoiced - already_returned
            $already_returned = 0;
            foreach ($this->invoice->materialReturns()->completed()->get()->pluck('lines')->flatten() as $returnedLine) {
                // check if line matches with invoiceLine
                if ($returnedLine->invoice_line_id !== $line->invoice_line_id) continue;
                // add already returned quantity for current line
                $already_returned += $returnedLine->quantity_movement;
            }

            // check if returning quantity > quantity available to return
            if ($line->quantity_movement > ($available = $quantity_invoiced - $already_returned))
                // reject with error
                return $this->documentError('inventory::material_return.lines.returning-gt-available', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                    'available' => $available,
                ]);

            // check that line has locator assigned
            if ($line->locator === null)
                // reject with error
                return $this->documentError('inventory::material_return.lines.no-locator', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]);
        }

        // return status InProgress
        return Document::STATUS_InProgress;
    }

    protected function updateStorage(Storage $storage, int &$quantityToMove):bool {
        // update stock on storage
        $storage->fill([
            // add movement quantity to storage.onHand
            'onhand'    => $storage->onhand + $quantityToMove,
        ]);
        // set quantity to move to 0 (zero), all movement when to first location found
        $quantityToMove = 0;

        // save storage changes, and document error if failed
        return !$storage->save() ? $this->documentError( $storage->errors()->first() ) : true;
    }

    public static function createFromInvoice(int|Invoice $invoice, array $attributes = []):self {
        // make resource from Invoice
        $resource = self::makeFromInvoice($invoice, $attributes);

        // stop process if MaterialReturn can't be saved
        if (!$resource->save())
            // return error through document error
            return tap($resource, fn($resource) => $resource->documentError( $resource->errors()->first() ));

        // foreach lines
        foreach ($resource->lines as $line) {
            // link with parent
            $line->materialReturn()->associate($resource);
            // stop process if line can't be saved
            if (!$line->save())
                // return error through document error
                return tap($resource, fn($resource) => $resource->documentError( $line->errors()->first() ));
        }

        // return created MaterialReturn resource
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
            $resource->lines->push( $line = new MaterialReturnLine($invoiceLine) );
            // set first locator of Product|Variant
            $line->locator()->associate( ($invoiceLine->variant ?? $invoiceLine->product)->locators()->first() );
        });

        // return resource
        return $resource;
    }

}

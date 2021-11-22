<?php

namespace HDSSolutions\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

class InOut extends A_InOut {

    public function __construct(array|Order|Invoice $attributes = []) {
        // check if is instance of Order
        if (($order = $attributes) instanceof Order) $attributes = self::fromResource($order, 'order_id');
        // check if is instance of Invoice
        if (($invoice = $attributes) instanceof Invoice) $attributes = self::fromResource($invoice, 'invoice_id');
        // redirect attributes to parent
        parent::__construct((is_array($attributes) ? $attributes : []) + [
            // force material_return=false
            'is_material_return'    => false,
        ]);
        // allow void this document
        $this->document_enableVoidIt = true;
    }

    protected static function booted() {
        static::addGlobalScope('in_out', fn(Builder $query) => $query->where('is_material_return', false));
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function lines() {
        return $this->hasMany(InOutLine::class);
    }

    protected function completeIt_updateStorage(Storage $storage, int &$quantityToMove):bool {
        logger("Storage $storage");

        // if document is_sale, substract stock from Storage
        if ($this->is_sale) {
            // get available onhand stock on current storage
            $available = $storage->reserved > $quantityToMove ? $quantityToMove : $storage->reserved;
            // check if no qty available on this storage
            if (!$available) return true;

            logger("Substracting $available from $storage");
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
            // check if no qty available on this storage
            if (!$received) return true;

            logger("Adding $received to $storage");
            // move pending to onhand stock on storage
            $storage->fill([
                // substract movement quantity from storage.pending
                'pending'   => $storage->pending - $received,
                // add movement quantity to storage.onHand
                'onhand'    => $storage->onhand + $received,
            ]);

            // substract received quantity from total quantity to move
            $quantityToMove -= $received;
        }

        // save storage changes, and document error if failed
        return $storage->save() || $this->documentError( $storage->errors()->first() ) === null;
    }

    public function voidIt():bool {
        // check if document wasn't completed
        // if so, no extra process needed
        if (!$this->wasCompleted())
            // not completed InOut didn't do anything yet
            // we are safe to complete the voidIt process
            return true;

        // if document was completed and is sale
        // reject it, stock must return through MaterialReturn document
        if ($this->is_sale)
            // reject process
            return $this->documentError('inventory::in_outs.voidIt.already-completed') === null;

        // document is purchase, process lines reverting received stock
        foreach ($this->lines as $line) {
            logger(__('Reverting line #:line of '.class_basename(static::class).' #:id: :product :variant', [
                'line'  => $line->id,
                'id'    => $this->id,
                'product'   => $line->product->name,
                'variant'   => $line->variant?->sku,
            ]));

            // save total quantity to revert
            $quantityToRevert = $line->quantity_movement;

            // get Variant|Product locators
            foreach (($line->variant ?? $line->product)->locators as $locator) {
                // check if locator belongs to current branch
                if ($locator->warehouse->branch_id !== $this->branch_id) continue;
                // revert storage
                if (!$this->voidIt_updateStorage(Storage::getFromProductOnLocator($line->product, $line->variant, $locator), $quantityToRevert))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already reverted and exit loop
                if ($quantityToRevert == 0) break;
            }

            // revert stock for Variant|Product on existing Storages
            foreach (Storage::getFromProduct($line->product, $line->variant, $this->branch) as $storage) {
                // revert existing storage
                if (!$this->voidIt_updateStorage($storage, $quantityToRevert))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already reverted and exit loop
                if ($quantityToRevert == 0) break;
            }

            // if not all movement quantity can be reverted, reject process
            if ($quantityToRevert > 0)
                // return document error
                return $this->documentError('inventory::in_outs.voidIt.lines.no-stock', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]) === null;

            // revert InvoiceLine.quantity_received
            if (!$line->invoiceLine->update([ 'quantity_received' => null ]))
                // return document error
                return $this->documentError( $line->invoiceLine->errors()->first() ) === null;
        }

        // document voided
        return true;
    }

    private function voidIt_updateStorage(Storage $storage, int &$quantityToMove):bool {
        logger("Storage $storage");

        // get available onhand stock on current storage
        $onhandToRevent = $storage->onhand > $quantityToMove ? $quantityToMove : $storage->onhand;
        // check if no qty available on this storage
        if (!$onhandToRevent) return true;

        logger("Reverting $onhandToRevent to $storage");
        // move onhand to pending stock on storage
        $storage->fill([
            // revert movement quantity from storage.onHand
            'onhand'    => $storage->onhand - $onhandToRevent,
            // add movement quantity to storage.pending
            'pending'   => $storage->pending + $onhandToRevent,
        ]);

        // substract reverted quantity from total quantity to move
        $quantityToMove -= $onhandToRevent;

        // save storage changes, and document error if failed
        return $storage->save() || $this->documentError( $storage->errors()->first() ) === null;
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
            $resource->lines->push( $line = new InOutLine($orderLine) );
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

        // create new resource from Order
        $resource = new self($invoice);
        // append extra attributes
        $resource->fill( $attributes );

        // create InvoiceLines from InvoiceLines
        $invoice->lines->each(function($invoiceLine) use ($resource) {
            // ignore line if product.type isn't stockable
            if (!$invoiceLine->product->stockable) return;
            // create a new InOutLine from InvoiceLine
            $resource->lines->push( $line = new InOutLine($invoiceLine) );
            // set first locator of Product|Variant
            $line->locator()->associate( ($invoiceLine->variant ?? $invoiceLine->product)->locators()->first() );
        });

        // return resource
        return $resource;
    }

}

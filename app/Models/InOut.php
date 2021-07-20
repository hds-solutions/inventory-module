<?php

namespace HDSSolutions\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

class InOut extends A_InOut {

    public function __construct(array|Order $attributes = []) {
        // check if is instance of Order
        if (($order = $attributes) instanceof Order) $attributes = self::fromResource($order, 'order_id');
        // redirect attributes to parent
        parent::__construct(is_array($attributes) ? $attributes : [] + [
            // force material_return=false
            'is_material_return'    => false,
        ]);
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

    protected function updateStorage(Storage $storage, int &$quantityToMove):bool {
        logger("Storage $storage");

        // if document is_sale, substract stock from Storage
        if ($this->is_sale) {
            // get available onhand stock on current storage
            $available = $storage->reserved > $quantityToMove ? $quantityToMove : $storage->reserved;
            // check if no qty available on this storage
            if (!$available) return true;
            //
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
            //
            logger("Adding $available to $storage");
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
            $resource->lines->push( $line = new InOutLine($orderLine) );
            // set first locator of Product|Variant
            $line->locator()->associate( ($orderLine->variant ?? $orderLine->product)->locators()->first() );
        });

        // return resource
        return $resource;
    }

}

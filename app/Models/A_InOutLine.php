<?php

namespace HDSSolutions\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

abstract class A_InOutLine extends X_InOutLine {

    protected final static function fromResourceLine(OrderLine|InvoiceLine $resourceLine, string $relation, string $quantity_field):array {
        // copy attributes from resource line
        return [
            $relation           => $resourceLine->id,
            'product_id'        => $resourceLine->product_id,
            'variant_id'        => $resourceLine->variant_id,
            'quantity_ordered'  => $resourceLine->$quantity_field,
            'quantity_movement' => $resourceLine->$quantity_field,
        ];
    }

    protected abstract function header();

    public final function product() {
        return $this->belongsTo(Product::class);
    }

    public final function variant() {
        return $this->belongsTo(Variant::class);
    }

    public final function locator() {
        return $this->belongsTo(Locator::class);
    }

    // FIXME: where is this method used?
    // public function invoiceLines() {
    //     return $this->belongsToMany(InvoiceLine::class)
    //         ->using(InOutLineInvoiceLine::class)
    //         ->withPivot([ 'quantity_movement', 'quantity_invoiced' ])
    //         ->withTimestamps();
    // }

    public final function beforeSave(Validator $validator) {
        // check if product is stockable
        if (!$this->product->stockable)
            // reject line with error
            return $validator->errors()->add('product_id', __('inventory::in_out.lines.product-not-stockable', [
                'product'   => $this->product->name,
                'variant'   => $this->variant?->sku,
            ]));

        // check if InOut already has a line with current Variant|Product
        if (!$this->exists && $this->header->hasProduct( $this->product, $this->variant ))
            // reject line with error
            return $validator->errors()->add('product_id', __('inventory::in_out.lines.already-has-product', [
                'product'   => $this->product->name,
                'variant'   => $this->variant?->sku,
            ]));

        // check if there are drafted Inventories of Variant|Product
        if (Inventory::hasOpenForProduct( $this->product, $this->variant, $this->header->branch ))
            // reject line with error
            return $validator->errors()->add('product_id', __('inventory::in_out.lines.pending-inventories', [
                'product'   => $this->product->name,
                'variant'   => $this->variant?->sku,
            ]));
    }

}

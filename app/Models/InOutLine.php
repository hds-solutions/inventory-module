<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

class InOutLine extends X_InOutLine {

    public function __construct(array|OrderLine|InvoiceLine $attributes = []) {
        // check if is instance of OrderLine
        if (($orderLine = $attributes) instanceof OrderLine) $attributes = self::fromResourceLine($orderLine, 'order_line_id', 'quantity_ordered');
        // check if is instance of InvoiceLine
        if (($invoiceLine = $attributes) instanceof InvoiceLine) $attributes = self::fromResourceLine($invoiceLine, 'invoice_line_id', 'quantity_invoiced');
        // redirect attributes to parent
        parent::__construct(is_array($attributes) ? $attributes : []);
    }

    private static function fromResourceLine(OrderLine|InvoiceLine $resourceLine, string $relation, string $quantity_field):array {
        // copy attributes from resource line
        return [
            $relation           => $resourceLine->id,
            'product_id'        => $resourceLine->product_id,
            'variant_id'        => $resourceLine->variant_id,
            'quantity_ordered'  => $resourceLine->$quantity_field,
            'quantity_movement' => $resourceLine->$quantity_field,
        ];
    }

    public function inOut() {
        return $this->belongsTo(InOut::class);
    }

    public function orderLine() {
        return $this->belongsTo(OrderLine::class);
    }

    public function invoiceLine() {
        return $this->belongsTo(InvoiceLine::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(Variant::class);
    }

    public function locator() {
        return $this->belongsTo(Locator::class);
    }

    public function invoiceLines() {
        return $this->belongsToMany(InvoiceLine::class)
            ->using(InOutLineInvoiceLine::class)
            ->withPivot([ 'quantity_movement', 'quantity_invoiced' ])
            ->withTimestamps();
    }

    public function beforeSave(Validator $validator) {
        // check if product is stockable
        if (!$this->product->stockable)
            // reject line with error
            return $validator->errors()->add([
                'product_id'    => __('inventory::in_out.lines.product-not-stockable', [
                    'product'   => $this->product->name,
                    'variant'   => $this->variant?->sku,
                ])
            ]);

        // check if InOut already has a line with current Variant|Product
        if ($this->inOut->hasProduct( $this->product, $this->variant ))
            // reject line with error
            return $validator->errors()->add([
                'product_id'    => __('inventory::in_out.lines.already-has-product', [
                    'product'   => $this->product->name,
                    'variant'   => $this->variant?->sku,
                ])
            ]);

        // check if there are drafted Inventories of Variant|Product
        if (Inventory::hasOpenForProduct( $this->product, $this->variant, $this->inOut->branch ))
            // reject line with error
            return $validator->errors()->add([
                'product_id'    => __('inventory::in_out.lines.pending-inventories', [
                    'product'   => $this->product->name,
                    'variant'   => $this->variant?->sku,
                ])
            ]);
    }

}

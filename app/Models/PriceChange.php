<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Interfaces\Document;
use HDSSolutions\Laravel\Traits\HasDocumentActions;

class PriceChange extends X_PriceChange implements Document {
    use HasDocumentActions;

    public static function nextDocumentNumber():?string {
        // return next document number for specified stamping
        return str_increment(self::withTrashed()->max('document_number'));
    }

    public function lines() {
        return $this->hasMany(PriceChangeLine::class);
    }

    public function prepareIt():?string {
        // check if document has lines
        if (!$this->lines->count()) return $this->documentError( __('inventory::price_change.no-lines') );
        // foreach lines
        foreach ($this->lines as $line)
            // check if line has current qty set
            if ($line->price === null)
                // return error
                return $this->documentError( __('inventory::price_change.empty-price', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]) );
        // return status InProgress
        return self::STATUS_InProgress;
    }

    public function approveIt():bool {
        // mark document as approved
        return true;
    }

    public function rejectIt():bool {
        // mark document as rejected
        return true;
    }

    public function completeIt():?string {
        // check if the document is approved
        if (!$this->isApproved()) return $this->documentError( __('inventory::price_change.completeIt.not-approved') );

        // foreach lines
        foreach ($this->lines as $line) {
            // get product/variant
            $resource = $line->variant ?? $line->product;
            // update ProductPrice
            if (!$resource->prices()->updateExistingPivot( $line->currency_id, [
                'cost'  => $line->cost,
                'price' => $line->price,
                'limit' => $line->limit,
            ]))
                // if failed, ProductPrice doesnt existe, create a new one
                $resource->prices()->attach( $line->currency_id, [
                    'product_id'    => $line->product_id,
                    'variant_id'    => $line->variant_id,
                    'currency_id'   => $line->currency_id,
                    'cost'  => $line->cost,
                    'price' => $line->price,
                    'limit' => $line->limit,
                ]);
        }

        // return completed status
        return self::STATUS_Completed;
    }

}

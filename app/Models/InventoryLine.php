<?php

namespace HDSSolutions\Finpar\Models;

class InventoryLine extends X_InventoryLine {

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function locator() {
        return $this->belongsTo(Locator::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(Variant::class);
    }

    public static function getFromProduct(Product $product, ?Variant $variant = null, ?Branch $branch = null):Collection {
        // get storages from product
        $storages = self::where('product_id', $product->id);
        // filter variant if is specified
        if ($variant !== null)
            // get storages that have product variant
            $storages->where('variant_id', $variant->id);
        // filter branch if is specified
        if ($branch !== null)
            // get storages that have locators on the branch (linked thought warehouse)
            $storages->whereIn('locator_id', Locator::whereIn('warehouse_id', Warehouse::where('branch_id', $branch->id)->pluck('id'))->pluck('id') );
        // return storages
        return $storages->get();
    }

}

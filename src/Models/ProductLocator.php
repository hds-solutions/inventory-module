<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Collection;

class ProductLocator extends X_ProductLocator {

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function locator() {
        return $this->belongsTo(Locator::class);
    }

    public static function isLocationEnabled(Product $product, Locator $locator):bool {
        // recorremos las ubicaciones del articulo
        foreach ($product->locators as $pLocator)
            // verificamos si es la ubicacion y esta habilitada
            if ($pLocator->id == $locator->id && $pLocator->pivot->active)
                // retornamos true
                return true;
        // retornamos false
        return false;
    }

    public static function getFromProduct(Product $product, ?Variant $variant = null, ?Branch $branch = null):Collection {
        // get locators from product
        $locators = self::where('product_id', $product->id);
        // filter variant if is specified
        if ($variant !== null)
            // get storages that have product variant
            $locators->where('variant_id', $variant->id);
             // filter branch is specified
        if ($branch !== null)
            // get locators that have locators on the branch (linked thought warehouse)
            $locators->whereIn('locator_id', Locator::whereIn('warehouse_id', Warehouse::where('branch_id', $branch->id)) );
        // return locators
        return $locators->get();
    }

}

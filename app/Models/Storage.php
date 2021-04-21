<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Collection;

class Storage extends X_Storage {

    public function locator() {
        return $this->belongsTo(Locator::class)->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant() {
        return $this->belongsTo(Variant::class)->withTrashed();
    }

    private static $cache = [];
    public static function getQtyAvailable(Product $product, ?Variant $variant = null, ?Branch $branch = null, bool $with_reserved = false, bool $cache = true):int {
        // check cache
        if (array_key_exists($key = implode('|', [
            $product->getKey(),
            $variant?->getKey() ?? '?',
            $branch?->getKey() ?? '?',
        ]), self::$cache) && $cache)
            // return from cache
            return self::$cache[ $key ];

        // available acumulator
        $qtyAvailable = 0;
        // foreach product storages
        foreach (self::getFromProduct($product, $variant, $branch) as $storage) {
            // // check if locator is enabled
            // if (ProductLocator::isLocationEnabled($product, $storage->locator))
                // add available quantuty
                $qtyAvailable += $with_reserved ? $storage->onhand + $storage->reserved : $storage->onhand - $storage->reserved;
        }
        // save to cache
        self::$cache[ $key ] = $qtyAvailable;

        // return available quantity
        return $qtyAvailable;
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

    public static function getFromProductOnLocator(Product $product, ?Variant $variant = null, Locator $locator):Storage {
        // get storage for product on locator
        $storage = self::where('product_id', $product->id);
        // check if variant was speficied
        if ($variant === null) $storage->whereNull('variant_id');
        else $storage->where('variant_id', $variant->id);
        // filter locator
        $storage->where('locator_id', $locator->id);
        // get first result
        if (($storage = $storage->first()) === null)
            // create new storage for product
            $storage = self::make([
                'locator_id'    => $locator->id,
                'product_id'    => $product->id,
                'variant_id'    => $variant->id ?? null,
                'onhand'        => 0,
            ]);
        // return storage
        return $storage;
    }

}

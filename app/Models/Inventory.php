<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Interfaces\Document;
use HDSSolutions\Laravel\Traits\HasDocumentActions;
use Illuminate\Database\Eloquent\Builder;

class Inventory extends X_Inventory implements Document {
    use HasDocumentActions;

    public static function hasOpenForProduct(Product|int $product, Variant|int $variant = null, Branch|int $branch = null):bool {
        // return if there are open Inventories with product|variant present
        return self::openForProduct($product, $variant, $branch)->count() > 0;
    }

    public static function nextDocumentNumber():?string {
        // return next document number for specified stamping
        return str_increment(self::withTrashed()->max('document_number'));
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function lines() {
        return $this->hasMany(InventoryLine::class);
    }

    public function scopeOpenForProduct(Builder $query, Product|int $product, Variant|int $variant = null, Branch|int $branch = null):Builder {
        // find open inventories
        $query = $this->scopeOpen($query);
        // check if branch is filtered
        if ($branch !== null) $query = $this->scopeOfBranch($query, $branch);
        // filter inventories that has Product|Variant line
        return $query->whereHas('lines', fn($line) => $line->ofProduct($product, $variant));
    }

    public function scopeOfBranch(Builder $query, Branch|int $branch):Builder {
        // return inventories of branch
        return $query->whereIn('warehouse_id', ($branch instanceof Branch ? $branch : Branch::find($branch))->warehouses->pluck('branch_id'));
    }

    public function prepareIt():?string {
        // check if document has lines
        if (!$this->lines()->count()) return $this->documentError( __('inventory::inventory.no-lines') );
        // foreach lines
        foreach ($this->lines as $line)
            // check if line has current qty set
            if ($line->counted === null)
                // return error
                return $this->documentError(__('inventory::inventory.line.empty-counted', [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]));

        // return status InProgress
        return Document::STATUS_InProgress;
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
        if (!$this->isApproved()) return $this->documentError( __('inventory::inventory.not-approved') );
        // foreach lines
        foreach ($this->lines as $line) {
            // get Storage for product+variant+locator
            $storage = Storage::getFromProductOnLocator($line->product, $line->variant, $line->locator);
            // update storage values
            $storage->fill([
                // replace onhand quantity with counted
                'onhand'        => $line->counted,
                // set expiration date
                'expire_at'     => $line->expire_at,
                // set last inventoried date to today
                'inventoried'   => now(),
            ]);
            // save and check errors
            if (!$storage->save())
                // return invalid document status
                return $this->documentError( $storage->errors()->first() );
        }

        // return completed status
        return Document::STATUS_Completed;
    }

    public function createInventoryLines():bool {
        // foreach all products
        foreach (Product::with([ 'variants.locators', 'locators' ])->get() as $product)

            // check if product has variants
            if (count($product->variants) == 0) {
                // get current storages for product without variants
                foreach (Storage::getFromProduct($product, null, $this->warehouse->branch) as $storage) {
                    // check if line exists
                    if ($this->lines->first(function($line) use ($product, $storage) {
                        // find line with product on locator
                        return $line->product_id == $product->id && $line->locator_id == $storage->locator_id;
                    // ignore locator, already exists
                    }) !== null) continue;

                    // create inventory line with product on locator
                    $inventoryLine = InventoryLine::create([
                        'inventory_id'  => $this->id,
                        'product_id'    => $product->id,
                        'locator_id'    => $storage->locator_id,
                        'current'       => $storage->onhand,
                    ]);
                    // return false is errors where found
                    if (count($inventoryLine->errors()) > 0) return false;
                }

                // get default locators for product
                foreach ($product->locators as $locator) {
                    // check if line exists
                    if ($this->lines->first(function($line) use ($product, $locator) {
                        // find line with product on locator
                        return $line->product_id == $product->id && $line->locator_id == $locator->id;
                    // ignore locator, already exists
                    }) !== null) continue;

                    // create inventory line with product on locator
                    $inventoryLine = InventoryLine::create([
                        'inventory_id'  => $this->id,
                        'product_id'    => $product->id,
                        'locator_id'    => $locator->id,
                        'current'       => 0,
                    ]);
                    // return false is errors where found
                    if (count($inventoryLine->errors()) > 0) return false;
                }

            } else {
                // add every variant of the product
                foreach ($product->variants as $variant) {
                    // get current storages for variant
                    foreach (Storage::getFromProduct($product, $variant, $this->warehouse->branche) as $storage) {
                        // check if line exists
                        if ($this->lines->first(function($line) use ($product, $variant, $storage) {
                            // find line with variant on locator
                            return $line->product_id == $product->id &&
                                $line->variant_id == $variant->id &&
                                $line->locator_id == $storage->locator_id;
                        }) !== null) continue;

                        // create inventory line with variant on locator
                        $inventoryLine = InventoryLine::create([
                            'inventory_id'  => $this->id,
                            'product_id'    => $product->id,
                            'variant_id'    => $variant->id,
                            'locator_id'    => $storage->locator_id,
                            'current'       => $storage->onhand,
                        ]);
                        // return false is errors where found
                        if (count($inventoryLine->errors()) > 0) return false;
                    }

                    // get default locators for variant
                    foreach ($variant->locators as $locator) {
                        // check if line exists
                        if ($this->lines->first(function($line) use ($product, $variant, $locator) {
                            // find line with product on locator
                            return $line->product_id == $product->id &&
                                $line->variant_id == $variant->id &&
                                $line->locator_id == $locator->id;
                        // ignore locator, already exists
                        }) !== null) continue;

                        // create inventory line with product on locator
                        $inventoryLine = InventoryLine::create([
                            'inventory_id'  => $this->id,
                            'product_id'    => $product->id,
                            'variant_id'    => $variant->id,
                            'locator_id'    => $locator->id,
                            'current'       => 0,
                        ]);
                        // return false is errors where found
                        if (count($inventoryLine->errors()) > 0) return false;
                    }
                }
            }

        // all lines were created
        return true;
    }

}

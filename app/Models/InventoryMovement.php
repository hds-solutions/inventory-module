<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Interfaces\Document;
use HDSSolutions\Finpar\Traits\HasDocumentActions;
use Illuminate\Support\Facades\DB;

class InventoryMovement extends X_InventoryMovement implements Document {
    use HasDocumentActions;

    public function warehouse() {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function toWarehouse() {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id')->withTrashed();
    }

    public function lines() {
        return $this->hasMany(InventoryMovementLine::class);
    }

    public function prepareIt():?string {
        // check if document has lines
        if (!$this->lines()->count()) return $this->documentError( __('inventory::inventory_movement.no-lines') );
        // foreach lines
        foreach ($this->lines as $line) {
            // check if line has quantity set
            if ($line->quantity === null || $line->quantity == 0)
                // return error
                return $this->documentError( __('inventory::inventory_movement.lines.empty-quantity', [ 'product' => $line->product->name, 'variant' => $line->variant?->sku ]) );
            // check if line has toLocator set
            if ($line->toLocator === null)
                // return error
                return $this->documentError( __('inventory::inventory_movement.lines.empty-toLocator', [ 'product' => $line->product->name, 'variant' => $line->variant?->sku ]) );

            // check if product has open inventories in origin branch
            if (Inventory::hasOpenForProduct( $line->product, $line->variant, $this->warehouse->branch ))
                // return error
                return $this->documentError( __('inventory::inventory_movement.lines.has-open-inventories', [ 'product' => $line->product->name, 'variant' => $line->variant?->sku, 'branch' => $this->warehouse->branch->name ]) );
            // check if product has open inventories in destination branch
            if (Inventory::hasOpenForProduct( $line->product, $line->variant, $this->toWarehouse->branch ))
                // return error
                return $this->documentError( __('inventory::inventory_movement.lines.has-open-inventories', [ 'product' => $line->product->name, 'variant' => $line->variant?->sku, 'branch' => $this->toWarehouse->branch->name ]) );

            // check if product has enough stock
            if ($line->quantity > ($available = Storage::getQtyAvailable( $line->product, $line->variant, $this->warehouse->branch, with_reserved: true )))
                // return error
                return $this->documentError( __('inventory::inventory_movement.lines.no-enough-stock', [ 'product' => $line->product->name, 'variant' => $line->variant?->sku, 'available' => $available ]) );
        }
        // return status InProgress
        return Document::STATUS_InProgress;
    }

    public function approveIt():bool {
        // reserve storage
        foreach ($this->lines as $line) {
            // get origin Storage for product+variant+locator
            $storage = Storage::getFromProductOnLocator($line->product, $line->variant, $line->locator);
            // move quantity to reserved
            $storage->fill([
                'onhand'    => $storage->onhand - $line->quantity,
                'reserved'  => $storage->reserved + $line->quantity,
            ]);
            // save and check errors
            if (!$storage->save())
                // return invalid document status
                return $this->documentError( $storage->errors()->first() ) === null;
        }

        // mark document as approved
        return true;
    }

    public function rejectIt():bool {
        // check if document was approved
        if ($this->wasApproved())
            // reverse reserved storage
            foreach ($this->lines as $line) {
                // get origin Storage for product+variant+locator
                $storage = Storage::getFromProductOnLocator($line->product, $line->variant, $line->locator);
                // move quantity to onhand
                $storage->fill([
                    'onhand'    => $storage->onhand + $line->quantity,
                    'reserved'  => $storage->reserved - $line->quantity,
                ]);
                // save and check errors
                if (!$storage->save())
                    // return invalid document status
                    return $this->documentError( $storage->errors()->first() ) === null;
            }
        // mark document as rejected
        return true;
    }

    public function completeIt():?string {
        // check if the document is approved
        if (!$this->isApproved()) return $this->documentError( __('inventory::inventory_movement.not-approved') );

        // revalidate status of document through prepareIt()
        if (!$this->processIt( Document::ACTION_Prepare ))
            // error message already created by prepareIt()
            return null;

        // wrap process into transaction
        DB::beginTransaction();

        // foreach lines
        foreach ($this->lines as $line) {
            // get origin Storage for product+variant+locator
            $origin = Storage::getFromProductOnLocator($line->product, $line->variant, $line->locator);
            // substract reserved quantity
            $origin->fill([ 'reserved' => $origin->reserved - $line->quantity ]);
            // save and check errors
            if (!$origin->save())
                // return invalid document status
                return $this->documentError( $origin->errors()->first() );

            // get destination Storage for product+variant+locator
            $destination = Storage::getFromProductOnLocator($line->product, $line->variant, $line->toLocator);
            // add onhand quantity
            $destination->fill([ 'onhand' => $destination->onhand + $line->quantity ]);
            // save and check errors
            if (!$destination->save())
                // return invalid document status
                return $this->documentError( $destination->errors()->first() );
        }

        // process finished
        DB::commit();

        // return completed status
        return Document::STATUS_Completed;
    }

    public function createInventoryMovementLines():bool {
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
                    $inventoryLine = InventoryMovementLine::create([
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
                    $inventoryLine = InventoryMovementLine::create([
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
                        $inventoryLine = InventoryMovementLine::create([
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
                        $inventoryLine = InventoryMovementLine::create([
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

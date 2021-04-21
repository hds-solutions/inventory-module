<?php

namespace HDSSolutions\Finpar\Models;

class InventoryMovementLine extends X_InventoryMovementLine {

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function locator() {
        return $this->belongsTo(Locator::class);
    }

    public function toLocator() {
        return $this->belongsTo(Locator::class, 'to_locator_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(Variant::class);
    }

}

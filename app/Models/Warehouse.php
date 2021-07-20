<?php

namespace HDSSolutions\Laravel\Models;

class Warehouse extends X_Warehouse {

    public function branch() {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function locators() {
        return $this->hasMany(Locator::class)->ordered();
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

}

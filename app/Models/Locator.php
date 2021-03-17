<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Validation\Validator;

class Locator extends X_Locator {

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'storages')
            ->withPivot([ 'onhand', 'ordered', 'reserved', 'inventoried' ]);
    }

    protected function beforeSave(Validator $validator) {
        // check if the onlt locator on warehouse
        if ($this->warehouse->locators()->whereKeyNot( $this->id )->count() > 0) return;
        // force default because is the only one
        $this->default = true;
    }

    protected function afterSave() {
        // check if locator was set as default
        if (!$this->default) return;
        // update locators on warehouse
        $this->warehouse->locators()
            // filter current locator
            ->whereKeyNot( $this->id )
            // remove default flag
            ->update([ 'default' => false ]);
    }

}

<?php

namespace HDSSolutions\Laravel\Seeders;

class InventoryPermissionsSeeder extends Base\PermissionsSeeder {

    public function __construct() {
        parent::__construct('inventory');
    }

    protected function permissions():array {
        return [
            $this->resource('warehouses'),
            $this->resource('locators'),
            $this->resource('in_outs'),
            $this->document('in_outs'),
            $this->resource('inventories'),
            $this->document('inventories'),
            $this->resource('inventory_movements'),
            $this->document('inventory_movements'),
            $this->resource('pricechanges'),
            $this->document('pricechanges'),
        ];
    }

    protected function afterRun():void {
        // create Depositor role
        $this->role('Depositor', [
            'warehouses.*',
            'locators.*',
            'in_outs.*',
            'inventories.*',
            'inventory_movements.*',
            'pricechanges.*',
        ]);
    }

}

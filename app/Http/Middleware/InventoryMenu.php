<?php

namespace HDSSolutions\Finpar\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class InventoryMenu extends Base\Menu {

    public function handle($request, Closure $next) {
        // create a submenu
        $sub = backend()->menu()
            ->add(__('inventory::inventories.nav'), [
                'icon'  => 'cogs',
            ])->data('priority', 800);

        $this
            // append items to submenu
            ->warehouses($sub)
            ->locators($sub)
            ->inventories($sub)
            ->inventory_movements($sub)
            ->pricechanges($sub);

        // continue witn next middleware
        return $next($request);
    }

    private function warehouses(&$menu) {
        if (Route::has('backend.warehouses') && $this->can('warehouses'))
            $menu->add(__('inventory::warehouses.nav'), [
                'route'     => 'backend.warehouses',
                'icon'      => 'warehouses'
            ]);

        return $this;
    }

    private function locators(&$menu) {
        if (Route::has('backend.locators') && $this->can('locators'))
            $menu->add(__('inventory::locators.nav'), [
                'route'     => 'backend.locators',
                'icon'      => 'locators'
            ]);

        return $this;
    }

    private function inventories(&$menu) {
        if (Route::has('backend.inventories') && $this->can('inventories'))
            $menu->add(__('inventory::inventories.nav'), [
                'route'     => 'backend.inventories',
                'icon'      => 'inventories'
            ]);

        return $this;
    }

    private function inventory_movements(&$menu) {
        if (Route::has('backend.inventory_movements') && $this->can('inventory_movements'))
            $menu->add(__('inventory::inventory_movements.nav'), [
                'route'     => 'backend.inventory_movements',
                'icon'      => 'inventory_movements'
            ]);

        return $this;
    }

    private function pricechanges(&$menu) {
        if (Route::has('backend.pricechanges') && $this->can('pricechanges'))
            $menu->add(__('inventory::pricechanges.nav'), [
                'route'     => 'backend.pricechanges',
                'icon'      => 'pricechanges'
            ]);

        return $this;
    }

}

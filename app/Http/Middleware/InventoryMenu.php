<?php

namespace HDSSolutions\Finpar\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class InventoryMenu extends Base\Menu {

    public function handle($request, Closure $next) {
        // create a submenu
        $sub = backend()->menu()
            ->add(__('inventory::inventories.nav'), [
                'nickname'  => 'inventory',
                'icon'      => 'cogs',
            ])->data('priority', 800);

        $this
            // append items to submenu
            ->warehouses($sub)
            ->locators($sub)
            ->in_outs($sub)
            ->material_returns($sub)
            ->inventories($sub)
            ->inventory_movements($sub)
            ->price_changes($sub);

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

    private function in_outs(&$menu) {
        if (Route::has('backend.in_outs') && $this->can('in_outs'))
            $menu->add(__('inventory::in_outs.nav'), [
                'route'     => 'backend.in_outs',
                'icon'      => 'in_outs'
            ]);

        return $this;
    }

    private function material_returns(&$menu) {
        if (Route::has('backend.material_returns') && $this->can('material_returns'))
            $menu->add(__('inventory::material_returns.nav'), [
                'route'     => 'backend.material_returns',
                'icon'      => 'material_returns'
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

    private function price_changes(&$menu) {
        if (Route::has('backend.price_changes') && $this->can('price_changes'))
            $menu->add(__('inventory::price_changes.nav'), [
                'route'     => 'backend.price_changes',
                'icon'      => 'price_changes'
            ]);

        return $this;
    }

}

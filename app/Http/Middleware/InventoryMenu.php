<?php

namespace HDSSolutions\Finpar\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class InventoryMenu {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
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
            ->inventory($sub);

        // continue witn next middleware
        return $next($request);
    }

    private function warehouses(&$menu) {
        if (Route::has('backend.warehouses'))
            $menu->add(__('inventory::warehouses.nav'), [
                'route'     => 'backend.warehouses',
                'icon'      => 'warehouses'
            ]);

        return $this;
    }

    private function locators(&$menu) {
        if (Route::has('backend.locators'))
            $menu->add(__('inventory::locators.nav'), [
                'route'     => 'backend.locators',
                'icon'      => 'locators'
            ]);

        return $this;
    }

    private function inventory(&$menu) {
        if (Route::has('backend.inventories'))
            $menu->add(__('inventory::inventories.nav'), [
                'route'     => 'backend.inventories',
                'icon'      => 'inventory'
            ]);

        return $this;
    }

}

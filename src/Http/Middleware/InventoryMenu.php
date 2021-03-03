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
            ->add(__('inventory::nav'), [
                'icon'  => 'cogs',
            ])->data('priority', 800);

        // $this
        //     // append items to submenu
        //     ->inventory($sub);

        // continue witn next middleware
        return $next($request);
    }

    private function inventory(&$menu) {
        if (Route::has('backend.inventory'))
            $menu->add(__('inventory::inventory.nav'), [
                'route'     => 'backend.inventory',
                'icon'      => 'inventory'
            ]);

        return $this;
    }

}

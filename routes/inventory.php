<?php

use Illuminate\Support\Facades\Route;
use HDSSolutions\Finpar\Http\Controllers\{
    WarehouseController,
    LocatorController,
    InventoryController,
    InventoryMovementController,
    PriceChangeController,
};

Route::group([
    'prefix'        => config('backend.prefix'),
    'middleware'    => [ 'web', 'auth:'.config('backend.guard') ],
], function() {
    // name prefix
    $name_prefix = [ 'as' => 'backend' ];

    Route::resource('warehouses',       WarehouseController::class,     $name_prefix)
        ->parameters([ 'warehouses' => 'resource' ])
        ->name('index', 'backend.warehouses');

    Route::resource('locators',         LocatorController::class,       $name_prefix)
        ->parameters([ 'locators' => 'resource' ])
        ->name('index', 'backend.locators');

    Route::resource('inventories',      InventoryController::class,     $name_prefix)
        ->parameters([ 'inventories' => 'resource' ])
        ->name('index', 'backend.inventories');
    Route::post('inventories/stock',                        [ InventoryController::class, 'stock' ])
        ->name('backend.inventories.stock');
    Route::get('inventories/{resource}/import/{import}',    [ InventoryController::class, 'import'])
        ->name('backend.inventories.import');
    Route::post('inventories/{resource}/import/{import}',   [ InventoryController::class, 'doImport' ])
        ->name('backend.inventories.import');
    Route::post('inventories/{resource}/process',           [ InventoryController::class, 'processIt' ])
        ->name('backend.inventories.process');

    Route::resource('inventory_movements',  InventoryMovementController::class, $name_prefix)
        ->parameters([ 'inventory_movements' => 'resource' ])
        ->name('index', 'backend.inventory_movements');
    Route::post('inventory_movements/{resource}/process',   [ InventoryMovementController::class, 'processIt' ])
        ->name('backend.inventory_movements.process');

    Route::resource('pricechanges',     PriceChangeController::class,     $name_prefix)
        ->parameters([ 'pricechanges' => 'resource' ])
        ->name('index', 'backend.pricechanges');
    Route::post('pricechanges/price',                       [ PriceChangeController::class, 'price' ])
        ->name('backend.pricechanges.price');
    Route::get('pricechanges/{resource}/import/{import}',   [ PriceChangeController::class, 'import'])
        ->name('backend.pricechanges.import');
    Route::post('pricechanges/{resource}/import/{import}',  [ PriceChangeController::class, 'doImport'])
        ->name('backend.pricechanges.import');
    Route::post('pricechanges/{resource}/process',          [ PriceChangeController::class, 'processIt'])
        ->name('backend.pricechanges.process');

});

<?php

use Illuminate\Support\Facades\Route;
use HDSSolutions\Laravel\Http\Controllers\{
    WarehouseController,
    LocatorController,

    PurchaseInOutController,
    SaleInOutController,

    MaterialReturnController,
    InventoryController,
    InventoryMovementController,
    PriceChangeController,

    InventoryReportsController,
};

Route::group([
    'prefix'        => config('backend.prefix'),
    'middleware'    => [ 'web', 'auth:'.config('backend.guard') ],
], function() {
    // name prefix
    $name_prefix = [ 'as' => 'backend' ];

    Route::resource('warehouses',               WarehouseController::class, $name_prefix)
        ->parameters([ 'warehouses' => 'resource' ])
        ->name('index', 'backend.warehouses');

    Route::resource('locators',                 LocatorController::class,   $name_prefix)
        ->parameters([ 'locators' => 'resource' ])
        ->name('index', 'backend.locators');

    Route::resource('purchases/in_outs',                PurchaseInOutController::class, [ 'as' => 'backend.purchases' ])
        ->parameters([ 'in_outs' => 'resource' ])
        ->name('index', 'backend.purchases.in_outs')
        ->except([ 'create', 'store' ]);
    Route::post('purchases/in_outs/{resource}/process', [ PurchaseInOutController::class, 'processIt'])
        ->name('backend.purchases.in_outs.process');

    Route::resource('sales/in_outs',                    SaleInOutController::class, [ 'as' => 'backend.sales' ])
        ->parameters([ 'in_outs' => 'resource' ])
        ->name('index', 'backend.sales.in_outs')
        ->except([ 'create', 'store' ]);
    Route::post('sales/in_outs/{resource}/process',     [ SaleInOutController::class, 'processIt'])
        ->name('backend.sales.in_outs.process');

    Route::resource('material_returns',         MaterialReturnController::class,    $name_prefix)
        ->parameters([ 'material_returns' => 'resource' ])
        ->name('index', 'backend.material_returns');
    Route::post('material_returns/{resource}/process',  [ MaterialReturnController::class, 'processIt'])
        ->name('backend.material_returns.process');

    Route::resource('inventories',              InventoryController::class, $name_prefix)
        ->parameters([ 'inventories' => 'resource' ])
        ->name('index', 'backend.inventories');
    Route::post('inventories/stock',                        [ InventoryController::class, 'stock' ])
        ->name('backend.inventories.stock');
    Route::get('inventories/{resource}/import/{import}',    [ InventoryController::class, 'import'])
        ->name('backend.inventories.import');
    Route::post('inventories/{resource}/import/{import}',   [ InventoryController::class, 'doImport' ]);
        // ->name('backend.inventories.import');
    Route::post('inventories/{resource}/process',           [ InventoryController::class, 'processIt' ])
        ->name('backend.inventories.process');

    Route::resource('inventory_movements',  InventoryMovementController::class, $name_prefix)
        ->parameters([ 'inventory_movements' => 'resource' ])
        ->name('index', 'backend.inventory_movements');
    Route::post('inventory_movements/{resource}/process',   [ InventoryMovementController::class, 'processIt' ])
        ->name('backend.inventory_movements.process');

    Route::resource('price_changes',        PriceChangeController::class,     $name_prefix)
        ->parameters([ 'price_changes' => 'resource' ])
        ->name('index', 'backend.price_changes');
    Route::post('price_changes/price',                      [ PriceChangeController::class, 'price' ])
        ->name('backend.price_changes.price');
    Route::get('price_changes/{resource}/import/{import}',  [ PriceChangeController::class, 'import'])
        ->name('backend.price_changes.import');
    Route::post('price_changes/{resource}/import/{import}', [ PriceChangeController::class, 'doImport']);
        // ->name('backend.price_changes.import');
    Route::post('price_changes/{resource}/process',         [ PriceChangeController::class, 'processIt'])
        ->name('backend.price_changes.process');

    Route::get('reports/intentory/stock',   [ InventoryReportsController::class, 'stock' ],  $name_prefix)
        ->name('backend.reports.inventory.stock');

});

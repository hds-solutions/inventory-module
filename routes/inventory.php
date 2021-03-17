<?php

use HDSSolutions\Finpar\Http\Controllers\InventoryController;
use HDSSolutions\Finpar\Http\Controllers\LocatorController;
use HDSSolutions\Finpar\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

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

});
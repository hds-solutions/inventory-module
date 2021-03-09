<?php

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

});
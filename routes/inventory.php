<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => config('backend.prefix'),
    'middleware'    => [ 'web', 'auth:'.config('backend.guard') ],
], function() {
    // name prefix
    $name_prefix = [ 'as' => 'backend' ];

    // Route::resource('companies',    CompanyController::class,   $name_prefix)
    //     ->parameters([ 'companies' => 'resource' ])
    //     ->name('index', 'backend.companies');

});
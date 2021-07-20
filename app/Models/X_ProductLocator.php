<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_ProductLocator extends Base\Pivot {
    use BelongsToCompany;

    protected $table = 'locator_product';

    protected $orderBy = [
        'priority' => 'DESC'
    ];

    protected $fillable = [
        'product_id',
        'locator_id',
        'active',
    ];

    protected $casts = [
        'active'    => 'boolean',
    ];

}

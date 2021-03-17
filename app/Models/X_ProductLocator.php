<?php

namespace HDSSolutions\Finpar\Models;

use App\Models\Base\Pivot;
use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_ProductLocator extends Pivot {
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

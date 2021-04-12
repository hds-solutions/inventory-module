<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_InventoryLine extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'variant_id',
        'locator_id',
        'current',
        'counted',
        'expire_at',
    ];

}

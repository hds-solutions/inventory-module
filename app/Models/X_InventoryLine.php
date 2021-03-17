<?php

namespace HDSSolutions\Finpar\Models;

use App\Models\Base\Model;
use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_InventoryLine extends Model {
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

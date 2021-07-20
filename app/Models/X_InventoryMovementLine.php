<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_InventoryMovementLine extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'inventory_movement_id',
        'product_id',
        'variant_id',
        'locator_id',
        'to_locator_id',
        'quantity',
    ];

}

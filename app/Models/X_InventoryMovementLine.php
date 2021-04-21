<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_InventoryMovementLine extends Base\Model {
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

<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_InventoryMovement extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'to_warehouse_id',
        'description',
    ];

    public function getBranchIdAttribute() {
        return $this->warehouse->branch_id;
    }

    public function getToBranchIdAttribute() {
        return $this->toWarehouse->branch_id;
    }

}

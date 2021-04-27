<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_Inventory extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'description',
        'warehouse_id',
    ];

    public function getBranchIdAttribute() {
        return $this->warehouse->branch_id;
    }

}

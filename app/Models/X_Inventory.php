<?php

namespace HDSSolutions\Finpar\Models;

use App\Models\Base\Model;
use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_Inventory extends Model {
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

<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_Warehouse extends Base\Model {
    use BelongsToCompany;

    protected $orderBy = [
        'name' => 'ASC'
    ];

    protected $fillable = [
        'branch_id',
        'name',
    ];

}

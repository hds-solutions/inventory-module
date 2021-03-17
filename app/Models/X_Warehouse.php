<?php

namespace HDSSolutions\Finpar\Models;

use App\Models\Base\Model;
use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_Warehouse extends Model {
    use BelongsToCompany;

    protected $orderBy = [
        'name' => 'ASC'
    ];

    protected $fillable = [
        'branch_id',
        'name',
    ];

}

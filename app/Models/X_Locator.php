<?php

namespace HDSSolutions\Finpar\Models;

use App\Models\Base\Model;
use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_Locator extends Model {
    use BelongsToCompany;

    protected $orderBy = [
        'default'   => 'DESC',
        'x', 'y', 'z'
    ];

    protected $fillable = [
        'warehouse_id',
        'default',
        'x', 'y', 'z',
    ];

    public function getNameAttribute():string {
        return $this->x.' : '.$this->y.' : '.$this->z;
    }

}

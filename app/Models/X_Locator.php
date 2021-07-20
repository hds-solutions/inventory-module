<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_Locator extends Base\Model {
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

    public function __toString():string {
        return $this->name;
    }

}

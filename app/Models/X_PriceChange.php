<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_PriceChange extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'description',
    ];

    protected static $rules = [
        'description'   => [ 'required' ],
    ];

}

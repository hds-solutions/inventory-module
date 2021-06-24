<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_PriceChange extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'document_number',
        'description',
    ];

    protected static $rules = [
        'document_number'   => [ 'required', 'unique:price_changes,document_number,{id}' ],
        'description'       => [ 'required' ],
    ];

}

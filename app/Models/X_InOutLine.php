<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Builder;

abstract class X_InOutLine extends Base\Model {

    protected $fillable = [
        'in_out_id',
        'order_line_id',
        'product_id',
        'variant_id',
        'locator_id',
        'quantity_ordered',
        'quantity_movement',
    ];

    protected static array $rules = [
        'in_out_id'         => [ 'required' ],
        'order_line_id'     => [ 'required' ],
        'product_id'        => [ 'required' ],
        'variant_id'        => [ 'sometimes', 'nullable' ],
        'locator_id'        => [ 'sometimes', 'nullable' ],
        'quantity_ordered'  => [ 'required', 'min:0' ],
        'quantity_movement' => [ 'required', 'min:0' ],
    ];

}

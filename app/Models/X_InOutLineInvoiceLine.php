<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

abstract class X_InOutLineInvoiceLine extends Base\Pivot {
    use BelongsToCompany;

    protected $fillable = [
        'in_out_line_id',
        'invoice_line_id',
        'quantity_movement',
        'quantity_invoiced',
    ];

}

<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_InOutLineInvoiceLine extends Base\Pivot {
    use BelongsToCompany;

    protected $fillable = [
        'in_out_line_id',
        'invoice_line_id',
        'quantity_movement',
        'quantity_invoiced',
    ];

}

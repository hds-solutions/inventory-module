<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;

abstract class X_InOut extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'employee_id',
        'partnerable_id',
        'partnerable_type',
        'order_id',
        'invoice_id',
        'transacted_at',
        'document_number',
        'is_purchase',
        'is_material_return',
    ];

    protected $attributes = [
        'is_purchase'           => false,
        'is_material_return'    => false,
    ];

    public function getIsSaleAttribute():bool {
        return !$this->is_purchase;
    }

    public function getTransactedAtPrettyAttribute():string {
        return pretty_date($this->transacted_at, true);
    }

}

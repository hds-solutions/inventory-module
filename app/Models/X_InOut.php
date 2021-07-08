<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;
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
        'stamping',
        'document_number',
        'is_purchase',
        'is_material_return',
        'is_complete',
    ];

    protected $attributes = [
        'is_purchase'           => false,
        'is_material_return'    => false,
        'is_complete'           => false,
    ];

    public function isPurchase():bool {
        return $this->is_purchase;
    }

    public function getIsSaleAttribute():bool {
        return !$this->is_purchase;
    }

    public function isSale():bool {
        return $this->is_sale;
    }

    public function isMaterialReturn():bool {
        return $this->is_material_return;
    }

    public function isComplete():bool {
        return $this->is_complete;
    }

}

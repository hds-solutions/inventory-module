<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Traits\BelongsToCompany;

abstract class X_Inventory extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'document_number',
        'description',
        'warehouse_id',
    ];

    protected static array $rules = [
        'document_number'   => [ 'required', 'unique:inventories,document_number,{id}' ],
        'description'       => [ 'required' ],
        'warehouse_id'      => [ 'required' ],
    ];

    public function getBranchIdAttribute() {
        return $this->warehouse->branch_id;
    }

}

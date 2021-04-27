<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;

class X_Storage extends Base\Model {
    use BelongsToCompany;

    public function __construct(array $attributes = []) {
        //
        parent::__construct($attributes);
        // build order by RAW query
        $this->orderBy = \DB::raw('COALESCE(expire_at, "9999-12-31")');
        $this->orderDirection = 'DESC';
    }

    protected $fillable = [
        'locator_id',
        'product_id',
        'variant_id',
        'pending',
        'onhand',
        'reserved',
        'inventoried',
        'expire_at',
    ];

    protected $casts = [
        'pending'       => 'integer',
        'onhand'        => 'integer',
        'reserved'      => 'integer',
        'inventoried'   => 'datetime',
        'expire_at'     => 'datetime',
    ];

    protected function setKeysForSaveQuery($query) {
        $query->where('locator_id', $this->attributes['locator_id']);
        $query->where('product_id', $this->attributes['product_id']);
        if ($this->variant_id === null)
            $query->whereNull('variant_id');
        else
            $query->where('variant_id', $this->attributes['variant_id']);
        //
        return $query;
    }

    public function getAvailableAttribute():int {
        // return on hand minus reserved
        return $this->onhand - $this->reserved;
    }

}

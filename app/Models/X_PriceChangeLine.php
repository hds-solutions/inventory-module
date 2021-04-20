<?php

namespace HDSSolutions\Finpar\Models;

use HDSSolutions\Finpar\Traits\BelongsToCompany;

class X_PriceChangeLine extends Base\Model {
    use BelongsToCompany;

    protected $fillable = [
        'price_change_id',
        'product_id',
        'variant_id',
        'currency_id',
        'current_cost',
        'current_price',
        'current_limit',
        'cost',
        'price',
        'limit',
    ];

    public function getCurrentCostAttribute():int|float {
        return $this->attributes['current_cost'] / pow(10, $this->currency->decimals);
    }

    public function setCurrentCostAttribute(int|float $current_cost) {
        $this->attributes['current_cost'] = $current_cost * pow(10, $this->currency->decimals);
    }

    public function getCurrentPriceAttribute():int|float {
        return $this->attributes['current_price'] / pow(10, $this->currency->decimals);
    }

    public function setCurrentPriceAttribute(int|float $current_price) {
        $this->attributes['current_price'] = $current_price * pow(10, $this->currency->decimals);
    }

    public function getCurrentLimitAttribute():int|float {
        return $this->attributes['current_limit'] / pow(10, $this->currency->decimals);
    }

    public function setCurrentLimitAttribute(int|float $current_limit) {
        $this->attributes['current_limit'] = $current_limit * pow(10, $this->currency->decimals);
    }

    public function getCostAttribute():int|float {
        return $this->attributes['cost'] / pow(10, $this->currency->decimals);
    }

    public function setCostAttribute(int|float $cost) {
        $this->attributes['cost'] = $cost * pow(10, $this->currency->decimals);
    }

    public function getPriceAttribute():int|float {
        return $this->attributes['price'] / pow(10, $this->currency->decimals);
    }

    public function setPriceAttribute(int|float $price) {
        $this->attributes['price'] = $price * pow(10, $this->currency->decimals);
    }

    public function getLimitAttribute():int|float {
        return $this->attributes['limit'] / pow(10, $this->currency->decimals);
    }

    public function setLimitAttribute(int|float $limit) {
        $this->attributes['limit'] = $limit * pow(10, $this->currency->decimals);
    }

}

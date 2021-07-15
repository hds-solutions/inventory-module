<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

class InOutLine extends A_InOutLine {

    public function __construct(array|OrderLine $attributes = []) {
        // check if is instance of OrderLine
        if (($orderLine = $attributes) instanceof OrderLine) $attributes = self::fromResourceLine($orderLine, 'order_line_id', 'quantity_ordered');
        // redirect attributes to parent
        parent::__construct(is_array($attributes) ? $attributes : []);
    }

    public function header() { return $this->inOut(); }
    public function inOut() {
        return $this->belongsTo(InOut::class);
    }

    public function orderLine() {
        return $this->belongsTo(OrderLine::class);
    }

}

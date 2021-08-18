<?php

namespace HDSSolutions\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

class InOutLine extends A_InOutLine {

    public function __construct(array|OrderLine|InvoiceLine $attributes = []) {
        // check if is instance of OrderLine
        if (($orderLine = $attributes) instanceof OrderLine) $attributes = self::fromResourceLine($orderLine, 'order_line_id', 'quantity_ordered');
        // check if is instance of InvoiceLine
        if (($invoiceLine = $attributes) instanceof InvoiceLine) $attributes = self::fromResourceLine($invoiceLine, 'invoice_line_id', 'quantity_invoiced');
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

    public function invoiceLine() {
        return $this->belongsTo(InvoiceLine::class);
    }

}

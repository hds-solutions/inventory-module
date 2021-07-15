<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class MaterialReturnLine extends A_InOutLine {

    protected $table = 'in_out_lines';

    public function getForeignKey() {
        return Str::snake(class_basename(InOutLine::class)).'_'.$this->getKeyName();
    }

    public function __construct(array|InvoiceLine $attributes = []) {
        // check if is instance of InvoiceLine
        if (($invoiceLine = $attributes) instanceof InvoiceLine) $attributes = self::fromResourceLine($invoiceLine, 'invoice_line_id', 'quantity_invoiced');
        // redirect attributes to parent
        parent::__construct(is_array($attributes) ? $attributes : []);
    }

    public function header() { return $this->materialReturn(); }
    public function materialReturn() {
        return $this->belongsTo(MaterialReturn::class, 'in_out_id');
    }

    public function invoiceLine() {
        return $this->belongsTo(InvoiceLine::class);
    }

}

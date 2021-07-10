<?php

namespace HDSSolutions\Finpar\Models;

use Illuminate\Database\Eloquent\Collection;

class InOutLineInvoiceLine extends X_InOutLineInvoiceLine {

    public function inOutLine() {
        return $this->belongsTo(InOutLine::class);
    }

    public function invoiceLine() {
        return $this->belongsTo(InvoiceLine::class);
    }

}

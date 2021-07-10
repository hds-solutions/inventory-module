<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateInOutLineInvoiceLineTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // get schema builder
        $schema = DB::getSchemaBuilder();

        // replace blueprint
        $schema->blueprintResolver(fn($table, $callback) => new Blueprint($table, $callback));

        $schema->create('in_out_line_invoice_line', function(Blueprint $table) {
            $table->foreignTo('InOutLine');
            $table->foreignTo('InvoiceLine');
            $table->primary([ 'in_out_line_id', 'invoice_line_id' ]);
            $table->unsignedInteger('quantity_movement');
            $table->unsignedInteger('quantity_invoiced');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('in_out_line_invoice_line');
    }

}

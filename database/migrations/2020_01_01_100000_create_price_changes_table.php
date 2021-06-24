<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePriceChangesTable extends Migration {
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

        // create table
        $schema->create('price_changes', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->string('document_number');
            $table->string('description');
            // use table as document
            $table->asDocument();
        });

        // create table
        $schema->create('price_change_lines', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('PriceChange');
            $table->foreignTo('Product');
            $table->foreignTo('Variant')->nullable();
            $table->foreignTo('Currency');
            $table->unsignedInteger('current_cost');
            $table->unsignedInteger('current_price');
            $table->unsignedInteger('current_limit');
            $table->unsignedInteger('cost')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('price_change_lines');
        Schema::dropIfExists('price_changes');
    }

}

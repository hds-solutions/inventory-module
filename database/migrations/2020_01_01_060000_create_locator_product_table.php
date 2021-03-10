<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateLocatorProductTable extends Migration {
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
        $schema->create('locator_product', function(Blueprint $table) {
            $table->asPivot();
            $table->foreignTo('Company');
            $table->foreignTo('Locator');
            $table->foreignTo('Product');
            $table->foreignTo('Variant')->nullable();
            $table->unique([ 'locator_id', 'product_id', 'variant_id' ]);
            $table->boolean('active')->default(true);
            $table->priority()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('locator_product');
    }

}

<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateStoragesTable extends Migration {
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
        $schema->create('storages', function(Blueprint $table) {
            $table->foreignTo('Company');
            $table->foreignTo('Locator');
            $table->foreignTo('Product');
            $table->foreignTo('Variant')->nullable();
            $table->unique([ 'locator_id', 'product_id', 'variant_id' ]);
            $table->unsignedInteger('pending')->default(0);
            $table->unsignedInteger('onhand')->default(0);
            $table->unsignedInteger('reserved')->default(0);
            $table->dateTime('expire_at')->nullable();
            $table->timestamp('inventoried_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('storages');
    }

}

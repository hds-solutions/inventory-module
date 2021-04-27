<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration {
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
        $schema->create('inventories', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->string('description');
            $table->foreignTo('Warehouse');
            // use table as document
            $table->asDocument();
        });

        // create table
        $schema->create('inventory_lines', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('Inventory');
            $table->foreignTo('Locator');
            $table->foreignTo('Product');
            $table->foreignTo('Variant')->nullable();
            $table->unsignedInteger('current');
            $table->unsignedInteger('counted')->nullable();
            $table->dateTime('expire_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventory_lines');
        Schema::dropIfExists('inventories');
    }

}

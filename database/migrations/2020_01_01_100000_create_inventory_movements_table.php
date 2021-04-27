<?php

use HDSSolutions\Finpar\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementsTable extends Migration {
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
        $schema->create('inventory_movements', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('Warehouse');
            $table->foreignTo('Warehouse', 'to_warehouse_id');
            $table->string('description');
            // use table as document
            $table->asDocument();
        });

        // create table
        $schema->create('inventory_movement_lines', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('InventoryMovement');
            $table->foreignTo('Product');
            $table->foreignTo('Variant')->nullable();
            $table->foreignTo('Locator');
            $table->foreignTo('Locator', 'to_locator_id')->nullable();
            $table->unsignedInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventory_movement_lines');
        Schema::dropIfExists('inventory_movements');
    }

}

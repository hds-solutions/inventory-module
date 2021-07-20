<?php

use HDSSolutions\Laravel\Blueprints\BaseBlueprint as Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration {
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
        $schema->create('warehouses', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('Branch');
            $table->string('name');
        });

        // create locators table
        $schema->create('locators', function(Blueprint $table) {
            $table->id();
            $table->foreignTo('Company');
            $table->foreignTo('Warehouse');
            $table->string('x')->default(0);
            $table->string('y')->default(0);
            $table->string('z')->default(0);
            $table->boolean('default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('locators');
        Schema::dropIfExists('warehouses');
    }

}

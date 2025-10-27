<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dish_variations', function (Blueprint $table) {
            $table->id();

            $table->integer("minimum_variation_required");
            $table->integer("no_of_varation_allowed");
            $table->unsignedBigInteger("type_id")->nullable();
            $table->unsignedBigInteger("dish_id")->nullable();
            $table->unsignedBigInteger("order_number")->nullable();
          

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dish_variations');
    }
}

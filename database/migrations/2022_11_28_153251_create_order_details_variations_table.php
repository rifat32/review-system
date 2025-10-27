<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_details_dish_id")->nullable();
             $table->unsignedBigInteger("order_details_id")->nullable();
             $table->foreign('order_details_id')->references('id')->on('order_details')->onDelete('cascade');
            $table->unsignedBigInteger("qty")->nullable();
            $table->unsignedBigInteger("variation_id")->nullable();
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
        Schema::dropIfExists('order_details_variations');
    }
}

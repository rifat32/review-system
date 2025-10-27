<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string("type")->nullable();
            $table->decimal("dish_price",10,2)->nullable();
            $table->decimal("main_price",10,2)->nullable();
            $table->decimal("qty",10,2)->nullable();
            $table->unsignedBigInteger("order_id")->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger("dish_id")->nullable();
            $table->unsignedBigInteger("meal_id")->nullable();
            $table->string("custom_id")->nullable();

            $table->decimal("discount_percentage",10,2)->nullable();
            $table->decimal("discounted_price_to_show",10,2)->nullable();


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
        Schema::dropIfExists('order_details');
    }
}

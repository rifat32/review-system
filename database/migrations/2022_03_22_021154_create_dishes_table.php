<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // @@@test update
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->decimal("price",10,2);

            $table->decimal("take_away_discounted_price",10,2);
            $table->decimal("eat_in_discounted_price",10,2);
            $table->decimal("delivery_discounted_price",10,2);

            $table->unsignedBigInteger("restaurant_id")->nullable();
            $table->unsignedBigInteger("menu_id")->nullable();
            $table->string("image")->nullable();
            $table->string("description")->nullable();
            $table->decimal("take_away",10,2)->nullable();
            $table->decimal("delivery",10,2)->nullable();
            $table->string("type")->nullable();
            $table->string("ingredients")->nullable();
            $table->string("calories")->nullable();

            $table->unsignedBigInteger("order_number")->nullable();
            $table->integer("preparation_time")->nullable();


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
        Schema::dropIfExists('dishes');
    }
}

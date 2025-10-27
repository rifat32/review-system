<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();


            $table->string("order_app")->nullable();

            $table->decimal("table_number",10,2)->nullable();
            $table->date("date")->nullable();
            $table->unsignedBigInteger("restaurant_id")->nullable();
            $table->string("status")->nullable();
            $table->string("payment_status")->nullable();





            $table->decimal("total_due_amount",10,2)->default(0);
            $table->decimal("amount",10,2)->default(0);

            $table->decimal("final_price",10,2)->default(0);




            $table->decimal("discount",10,2)->default(0);
            $table->enum("discount_type",['fixed', 'percentage'])->default("fixed")->nullable();
            $table->decimal("tax",10,2)->default(0);






            $table->string("payment_method")->nullable();
            $table->string("remarks")->nullable();
            $table->string("type")->nullable();
            $table->string("autoprint")->nullable()->default(false);
            $table->string("customer_name")->nullable();
            $table->string("customer_phone")->nullable();
            $table->string("customer_post_code")->nullable();
            $table->string("customer_address")->nullable();
            $table->string("door_no")->nullable();
            $table->unsignedBigInteger("order_by")->nullable();
            $table->unsignedBigInteger("customer_id")->nullable();
            $table->decimal("cash",10,2)->nullable();
            $table->decimal("card",10,2)->nullable();

            $table->text("request_object")->nullable();

            $table->text("initial_note")->nullable();
            $table->text("customer_note")->nullable();


            $table->string("coupon_type")->nullable();
            $table->decimal("coupon_amount",10,2)->nullable();




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
        Schema::dropIfExists('orders');
    }
}

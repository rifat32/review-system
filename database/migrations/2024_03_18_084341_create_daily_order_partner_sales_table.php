<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyOrderPartnerSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_order_partner_sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_partner_id')->constrained("restaurant_partners")->onDelete("restrict");


            $table->integer('eat_in_orders');
            $table->decimal('eat_in_orders_amount',10,2);

            $table->integer('takeaway_orders');
            $table->decimal('takeaway_orders_amount',10,2);

            $table->text('notes')->nullable();
            $table->decimal('bank_payment',10,2);
            $table->decimal('cash_payment',10,2);
            $table->integer('delivery_orders');
            $table->decimal('delivery_orders_amount',10,2);

            $table->unsignedBigInteger('restaurant_id');


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
        Schema::dropIfExists('daily_order_partner_sales');
    }
}

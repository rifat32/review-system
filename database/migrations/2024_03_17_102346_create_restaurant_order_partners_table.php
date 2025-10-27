<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantOrderPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_order_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_partner_id')->constrained("restaurant_partners")->onDelete("restrict");
            $table->boolean('delivery');
            $table->decimal('delivery_order_commission', 8, 2)->nullable();
            $table->string('delivery_shop_link')->nullable();
            $table->boolean('eat_in');
            $table->decimal('eat_in_order_commission', 8, 2)->nullable();
            $table->string('eat_in_shop_link')->nullable();
            $table->boolean('takeaway');
            $table->decimal('takeaway_order_commission',10,2)->nullable();
            $table->string('takeaway_link')->nullable();
            $table->json('contact_details')->nullable();

            $table->string('api_key')->nullable(); // Changed: Added API key for integrations
            $table->text('payment_terms')->nullable(); // Changed: Added payment terms
            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('restaurant_order_partners');
    }
}

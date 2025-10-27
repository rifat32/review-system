<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("business_id");
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            $table->string("name");

            $table->enum("type", ['buy_one_get_one_same', 'buy_one_get_one_other', 'spend_certain_amount', 'time_based_discount','menu_discount']);

            $table->enum("discount_type",['fixed', 'percentage'])->default("fixed")->nullable();
            $table->decimal("discount_amount", 10, 2)->nullable(); // Required for spend_certain_amount, time_based_discount


            $table->integer("max_redemptions");
            $table->integer("customer_redemptions");

            $table->dateTime("campaign_start_date"); // Required for all types
            $table->dateTime("campaign_end_date"); // Required for all types

            $table->time("campaign_start_time")->nullable(); // Required for time_based_discount
            $table->time("campaign_end_time")->nullable(); // Required for time_based_discount


            $table->boolean("is_active")->default(0); // Required for all types

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
        Schema::dropIfExists('campaigns');
    }
}

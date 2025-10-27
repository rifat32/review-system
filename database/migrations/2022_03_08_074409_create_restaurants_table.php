<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();


            $table->boolean("show_image")->nullable()->default(1);
            $table->decimal("tax_percentage",10,2)->nullable()->default(0);


            $table->string("Name");
            $table->text("About")->nullable();
            $table->string("Webpage")->nullable();
            $table->string("PhoneNumber")->nullable();
            $table->string("EmailAddress")->nullable();
            $table->text("homeText")->nullable();
            $table->string("AdditionalInformation")->nullable();
            $table->string("GoogleMapApi")->nullable();

            $table->string("Address");
            $table->string("PostCode");
            $table->string("Logo")->nullable();
            $table->unsignedBigInteger("OwnerID");
            $table->string("Key_ID")->nullable();
            $table->date("expiry_date")->nullable();
            $table->integer("totalTables")->default(0);
            $table->string("Status")->nullable();
            $table->string("Layout")->nullable();

            $table->boolean("enable_question");


            $table->boolean("is_eat_in")->default(false);
            $table->boolean("is_delivery")->default(false);
            $table->boolean("is_take_away")->default(false);
            $table->boolean("is_customer_order")->default(false);


            $table->boolean("Is_guest_user")->default(false);
            $table->boolean("is_review_silder")->default(false);
            $table->boolean("review_only")->default(true)->nullable(false);


            $table->string("review_type")->default("star")->nullable(false);

            $table->text("google_map_iframe")->nullable();



            $table->boolean("is_business_type_restaurant")->default(true)->nullable(false);


            $table->string("business_type")->nullable(true);

            $table->string("header_image")->default("/header_image/default.webp");
            $table->string("rating_page_image")->default("/rating_page_image/default.webp");
            $table->string("placeholder_image")->default("/placeholder_image/default.webp");


            $table->string("menu_pdf")->nullable(true);




            $table->boolean("is_pdf_manu")->default(true)->nullable(false);



            $table->string("primary_color")->nullable(true);
            $table->string("secondary_color")->nullable(true);



            $table->string("client_primary_color")->nullable(true)->default("#172c41");
            $table->string("client_secondary_color")->nullable(true)->default("#ac8538");
            $table->string("client_tertiary_color")->nullable(true)->default("#fffffff");

            $table->boolean("user_review_report")->default(0);
            $table->boolean("guest_user_review_report")->default(0);



            $table->string("pin")->nullable(true);



            $table->boolean("enable_customer_order_payment")->nullable(true)->default(0);






            $table->string("STRIPE_KEY")->nullable(true);
            $table->string("STRIPE_SECRET")->nullable(true);




            $table->json("eat_in_payment_mode")->nullable(true);
            $table->json("takeaway_payment_mode")->nullable(true);
            $table->json("delivery_payment_mode")->nullable(true);



            $table->boolean("is_customer_order_enabled")->nullable(true);
           $table->boolean("is_report_email_enabled")->nullable(true)->default(0);


            $table->softDeletes();
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
        Schema::dropIfExists('restaurants');
    }
}

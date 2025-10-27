<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('generated_id')->nullable();

            $table->decimal("amount",10,2)->default(0);
            $table->string("payment_method");
            $table->date("payment_date");
            $table->string("note")->nullable();
            $table->string("shareable_link");
            $table->string("paid_by");


            $table->text("description")->nullable();
            $table->string("expense_type");
            $table->json("reciepts");

            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('receipt_by')->nullable();




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
        Schema::dropIfExists('expenses');
    }
}

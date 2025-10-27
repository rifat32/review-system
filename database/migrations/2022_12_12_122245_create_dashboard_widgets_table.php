<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardWidgetsTable extends Migration
{

    public function up()
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description");
            $table->string("user_type");
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('dashboard_widgets');
    }
}

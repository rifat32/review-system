<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTimeBasedToDishesTable extends Migration
{
  public function up(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->boolean('is_time_based')->default(false)->after('id');
            $table->boolean('show_in_future_date')->default(true)->after('is_time_based');
        });
    }

    public function down(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->dropColumn(['is_time_based', 'show_in_future_date']);
        });
    }
}

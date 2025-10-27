<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewFieldsToBusinessesTable extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('is_review_silder')->default(false)->after('is_eat_in');
            $table->boolean('review_only')->default(true)->nullable(false)->after('is_review_silder');
            $table->string('review_type')->default('star')->nullable(false)->after('review_only');
            $table->boolean('user_review_report')->default(false)->after('review_type');
            $table->boolean('guest_user_review_report')->default(false)->after('user_review_report');
            $table->string('header_image')->default('/header_image/default.webp')->after('guest_user_review_report');
            $table->string('rating_page_image')->default('/rating_page_image/default.webp')->after('header_image');
            $table->string('placeholder_image')->default('/placeholder_image/default.webp')->after('rating_page_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'is_review_silder',
                'review_only',
                'review_type',
                'user_review_report',
                'guest_user_review_report',
                'header_image',
                'rating_page_image',
                'placeholder_image',
            ]);
        });
    }


}

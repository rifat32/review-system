<?php






use App\Http\Controllers\CustomerController;

use App\Http\Controllers\ReportController;

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewNewController;
use App\Http\Controllers\SuperAdminReportController;
use App\Http\Controllers\DailyViewsController;





use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Define route for GET method
Route::get('/health', function () {
    return response()->json(['status' => 'Server is up and running'], 200);
});

// Define route for POST method
Route::post('/health', function () {
    return response()->json(['status' => 'Server is up and running'], 200);
});

// Define route for PUT method
Route::put('/health', function () {
    return response()->json(['status' => 'Server is up and running'], 200);
});

// Define route for DELETE method
Route::delete('/health', function () {
    return response()->json(['status' => 'Server is up and running'], 200);
});

// Define route for PATCH method
Route::patch('/health', function () {
    return response()->json(['status' => 'Server is up and running'], 200);
});



Route::get('/review-new/get/questions-all/customer', [ReviewNewController::class, "getQuestionAllUnauthorized"]);


Route::get('/review-new/get/questions-all-report/unauthorized', [ReviewNewController::class, "getQuestionAllReportUnauthorized"]);

Route::get('/review-new/get/questions-all-report/guest/unauthorized', [ReviewNewController::class, "getQuestionAllReportGuestUnauthorized"]);




Route::post('/review-new-guest/{restaurantId}', [ReviewNewController::class, "storeReviewByGuest"]);


// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// @@@@@@@@@@@@@@@@@@@@  Protected Routes      @@@@@@@@@@@@@@@@@
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
Route::middleware(['auth:api'])->group(function () {




    // #################
    // dailyviews Routes

    // #################
    Route::post('/dailyviews/{restaurantId}', [DailyViewsController::class, "store"]);
    Route::patch('/dailyviews/update/{restaurantId}', [DailyViewsController::class, "update"]);



    // #################
    // review  Routes
    // #################

    Route::post('/review/reviewvalue/{restaurantId}/{rate}', [ReviewController::class, "store"]);
    Route::get('/review/getvalues/{restaurantId}/{rate}', [ReviewController::class, "getReviewValues"]);
    Route::get('/review/getvalues/{restaurantId}', [ReviewController::class, "getreviewvalueById"]);
    Route::get('/review/getavg/review/{restaurantId}/{start}/{end}', [ReviewController::class, "getAverage"]);
    Route::post('/review/addupdatetags/{restaurantId}', [ReviewController::class, "store2"]);

    Route::get('/review/getreview/{restaurantId}/{rate}/{start}/{end}', [ReviewController::class, "filterReview"]);
    Route::get('/review/getreviewAll/{restaurantId}', [ReviewController::class, "getReviewByRestaurantId"]);
    Route::get('/review/getcustomerreview/{restaurantId}/{start}/{end}', [ReviewController::class, "getCustommerReview"]);
    Route::post('/review/{restaurantId}', [ReviewController::class, "storeReview"]);

    // #################
    // review new  Routes
    // #################

    Route::post('/review-new/reviewvalue/{restaurantId}/{rate}', [ReviewNewController::class, "store"]);
    Route::get('/review-new/getvalues/{restaurantId}/{rate}', [ReviewNewController::class, "getReviewValues"]);
    Route::get('/review-new/getvalues/{restaurantId}', [ReviewNewController::class, "getreviewvalueById"]);
    Route::get('/review-new/getavg/review/{restaurantId}/{start}/{end}', [ReviewNewController::class, "getAverage"]);
    Route::post('/review-new/addupdatetags/{restaurantId}', [ReviewNewController::class, "store2"]);

    Route::get('/review-new/getreview/{restaurantId}/{rate}/{start}/{end}', [ReviewNewController::class, "filterReview"]);
    Route::get('/review-new/getreviewAll/{restaurantId}', [ReviewNewController::class, "getReviewByRestaurantId"]);
    Route::get('/review-new/getcustomerreview/{restaurantId}/{start}/{end}', [ReviewNewController::class, "getCustommerReview"]);
    Route::post('/review-new/{restaurantId}', [ReviewNewController::class, "storeReview"]);

    // #################
    // question  Routes
    // #################
    Route::post('/review-new/create/questions', [ReviewNewController::class, "storeQuestion"]);
    Route::put('/review-new/update/questions', [ReviewNewController::class, "updateQuestion"]);
    Route::put('/review-new/update/active_state/questions', [ReviewNewController::class, "updateQuestionActiveState"]);

    Route::get('/review-new/get/questions', [ReviewNewController::class, "getQuestion"]);
    Route::get('/review-new/get/questions-all', [ReviewNewController::class, "getQuestionAll"]);


    Route::get('/review-new/get/questions-all-report', [ReviewNewController::class, "getQuestionAllReport"]);
    Route::get('/review-new/get/questions-all-report/guest', [ReviewNewController::class, "getQuestionAllReportGuest"]);


    Route::get('/review-new/get/questions-all-report-by-user/{perPage}', [ReviewNewController::class, "getQuestionAllReportByUser"]);
    Route::get('/review-new/get/questions-all-report-by-user-guest/{perPage}', [ReviewNewController::class, "getQuestionAllReportByUserGuest"]);




    Route::get('/review-new/get/questions/{id}', [ReviewNewController::class, "getQuestionById"]);
    Route::get('/review-new/get/questions/{id}/{restaurantId}', [ReviewNewController::class, "getQuestionById2"]);
    Route::delete('/review-new/delete/questions/{id}', [ReviewNewController::class, "deleteQuestionById"]);

    // #################
    // tag  Routes
    // #################

    Route::post('/review-new/create/tags', [ReviewNewController::class, "storeTag"]);

    Route::post('/review-new/create/tags/multiple/{restaurantId}', [ReviewNewController::class, "storeTagMultiple"]);

    Route::put('/review-new/update/tags', [ReviewNewController::class, "updateTag"]);
    Route::get('/review-new/get/tags', [ReviewNewController::class, "getTag"]);
    Route::get('/review-new/get/tags/{id}', [ReviewNewController::class, "getTagById"]);
    Route::get('/review-new/get/tags/{id}/{reataurantId}', [ReviewNewController::class, "getTagById2"]);
    Route::delete('/review-new/delete/tags/{id}', [ReviewNewController::class, "deleteTagById"]);
    // #################
    // Star Routes
    // #################
    Route::post('/review-new/create/stars', [ReviewNewController::class, "storeStar"]);
    Route::put('/review-new/update/stars', [ReviewNewController::class, "updateStar"]);
    Route::get('/review-new/get/stars', [ReviewNewController::class, "getStar"]);
    Route::get('/review-new/get/stars/{id}', [ReviewNewController::class, "getStarById"]);
    Route::get('/review-new/get/stars/{id}/{restaurantId}', [ReviewNewController::class, "getStarById2"]);
    Route::delete('/review-new/delete/stars/{id}', [ReviewNewController::class, "deleteStarById"]);





    Route::get('/review-new/get/questions-all-report/quantum', [ReviewNewController::class, "getQuestionAllReportQuantum"]);

    Route::get('/review-new/get/questions-all-report/guest/quantum', [ReviewNewController::class, "getQuestionAllReportGuestQuantum"]);




    // #################
    // Star tag question Routes
    // #################

    Route::post('/star-tag-question', [ReviewNewController::class, "storeStarTag"]);
    Route::put('/star-tag-question', [ReviewNewController::class, "updateStarTag"]);
    Route::get('/star-tag-question', [ReviewNewController::class, "getStarTag"]);
    Route::get('/star-tag-question/{id}', [ReviewNewController::class, "getStarTagById"]);
    Route::delete('/star-tag-question/{id}', [ReviewNewController::class, "deleteStarTagById"]);
    Route::get('/tag-count/star-tag-question/{restaurantId}', [ReviewNewController::class, "getSelectedTagCount"]);
    Route::get('/tag-count/star-tag-question/by-question/{questionId}', [ReviewNewController::class, "getSelectedTagCountByQuestion"]);








    Route::get('/v1.0/customers', [CustomerController::class, "getCustomers"]);






    Route::middleware(['superadmin'])->group(function () {

        Route::get('/superadmin/dashboard-report/today-reviews', [SuperAdminReportController::class, "getTodayReviews"]);
        Route::get('/superadmin/dashboard-report/review-report', [SuperAdminReportController::class, "getReviewReport"]);
        Route::get('/superadmin/dashboard-report/total-reviews', [SuperAdminReportController::class, "getTotalReviews"]);
    });


    Route::get('/customer-report', [ReportController::class, "customerDashboardReport"]);

    Route::get('/dashboard-report/{restaurantId}', [ReportController::class, "getDashboardReport"]);

    Route::get('/dashboard-report2', [ReportController::class, "getDashboardReport2"]);


    // #################
    // Review Owner Routes
    // #################

    Route::post('/review-new/owner/create/questions', [ReviewNewController::class, "storeOwnerQuestion"]);
    Route::patch('/review-new/owner/update/questions', [ReviewNewController::class, "updateOwnerQuestion"]);
});


// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// coupon management section
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

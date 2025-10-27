<?php



use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClientCouponController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomWebhookController;
use App\Http\Controllers\DailyOrderPartnerSaleController;
use App\Http\Controllers\DailyViewsController;
use App\Http\Controllers\DashboardManagementController;
use App\Http\Controllers\DashboardWidgetController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailTemplateWrapperController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LeafletController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantOrderPartnerController;
use App\Http\Controllers\RestaurantPartnerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewNewController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SuperAdminReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\VariationController;
use Illuminate\Http\Request;
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


Route::post('/create-payment-intent/{restaurantId}', [OrderController::class, 'createPaymentIntent']);

Route::get('/leaflet/get', [LeafletController::class, "getLeaflet"]);
Route::get('/leaflet/get/{id}', [LeafletController::class, "getLeafletById"]);

Route::post('/v1.0/leaflet-image', [LeafletController::class, "createLeafletImage"]);

// /review-new/get/questions-all-report/guest






Route::post('/owner/user/registration', [OwnerController::class, "createUser2"]);


Route::post('/owner/user/with/restaurant', [OwnerController::class, "createUserWithRestaurant"]);





Route::post('/owner/user/check/email', [OwnerController::class, "checkEmail"]);




Route::post('/owner/super/admin', [OwnerController::class, "createsuperAdmin"]);

Route::post('/owner', [OwnerController::class, "createUser"]);
// #################
// Owner Routes
// Authorization may be hide for some routes I do not know
// #################


// guest user
Route::post('/owner/guestuserregister', [OwnerController::class, "createGuestUser"]);
// end of guest user
Route::post('/owner/staffregister/{restaurantId}', [OwnerController::class, "createStaffUser"]);

Route::post('/owner/pin/{ownerId}', [OwnerController::class, "updatePin"]);

Route::get('/owner/{ownerId}', [OwnerController::class, "getOwnerById"]);

Route::get('/owner/getAllowner/withourrestaurant', [OwnerController::class, "getOwnerNotHaveRestaurent"]);

Route::get('/owner/loaduser/bynumber/{phoneNumber}', [OwnerController::class, "getOwnerByPhoneNumber"]);




Route::post('/order/restaurant-partner/create', [RestaurantOrderPartnerController::class, "createRestaurantOrderPartner"]);
Route::put('/order/restaurant-partner/update', [RestaurantOrderPartnerController::class, "updateRestaurantOrderPartner"]);
Route::get('/order/restaurant-partner/get-all/{restaurant_id}', [RestaurantOrderPartnerController::class, "getRestaurantOrderPartner"]);
Route::get('/order/restaurant-partner/get/{id}', [RestaurantOrderPartnerController::class, "getRestaurantOrderPartnerById"]);
Route::delete('/order/restaurant-partner/delete/{id}', [RestaurantOrderPartnerController::class, "deleteRestaurantOrderPartnerById"]);




Route::post('/order/daily-order-partner-sale/create', [DailyOrderPartnerSaleController::class, "createDailyOrderPartnerSale"]);
Route::put('/order/daily-order-partner-sale/update', [DailyOrderPartnerSaleController::class, "updateDailyOrderPartnerSale"]);
Route::get('/order/daily-order-partner-sale/get-all/{restaurant_id}', [DailyOrderPartnerSaleController::class, "getDailyOrderPartnerSale"]);
Route::get('/order/daily-order-partner-sale/get/{id}', [DailyOrderPartnerSaleController::class, "getDailyOrderPartnerSaleById"]);
Route::delete('/order/daily-order-partner-sale/delete/{id}', [DailyOrderPartnerSaleController::class, "deleteDailyOrderPartnerSaleById"]);






// #################
// menu  Routes

// #################
Route::post('/menu/csv/{restaurantId}', [MenuController::class, "storeMenuByCsv"]);

Route::post('/menu/{restaurantId}', [MenuController::class, "storeMenu"]);
Route::post('/menu/check/{restaurantId}', [MenuController::class, "checkMenu"]);

Route::patch('/menu/update/{MenuId}', [MenuController::class, "updateMenu"]);





Route::get('/menu/{menuId}', [MenuController::class, "getMenuById"]);
Route::get('/menu/by-restaurant/{menuId}/{restaurantId}', [MenuController::class, "getMenuById2"]);
// Route::get('/menu/get-menu-by-restaurant-id{restaurantId}', [MenuController::class, "getMenuByRestaurantId"]);
Route::get('/menus/all-info/{restaurantId}', [MenuController::class, "getMenuWithAllInfoByRestaurantId"]);

Route::get('/menu/AllbuId/{restaurantId}', [MenuController::class, "getMenuByRestaurantId"]);
Route::get('/menu/AllbuId/{restaurantId}/{perPage}', [MenuController::class, "getMenuByRestaurantIdWithPagination"]);




Route::post('/menu/multiple/{restaurantId}', [MenuController::class, "storeMultipleMenu"]);
Route::patch('/menu/Edit/multiple', [MenuController::class, "updateMultipleMenu"]);
Route::patch('/menu/Updatemenu', [MenuController::class, "updateMenu2"]);
Route::delete('/menu/{menuId}', [MenuController::class, "deleteMenu"]);


// #################
// dish  Routes
// #################


Route::post('/dishes/{menuId}', [DishController::class, "storeDish"]);
Route::patch('/dishes/UpdateDishesDetails/{dishId}', [DishController::class, "updateDish"]);
Route::post('/dishes/uploadimage/{dishId}', [DishController::class, "updateDishImage"]);
Route::get('/dishes/All/dishes/{restaurantId}', [DishController::class, "getAllDishes"]);
Route::get('/v2/dishes/All/dishes/{restaurantId}', [DishController::class, "getAllDishesV2"]);
Route::get('/dishes/All/dishes/{restaurantId}/{perPage}', [DishController::class, "getAllDishesWithPagination"]);
Route::get('/dishes/{menuId}', [DishController::class, "getDisuBuMenuId"]);

Route::get('/dishes/by-restaurant/{menuId}/{restaurantId}', [DishController::class, "getDisuBuMenuId2"]);

Route::get('/deal-dishes/by-restaurant/{menuId}/{restaurantId}', [DishController::class, "getDealDisuBuMenuId2"]);



Route::get('/dishes/getdealsdishes/{dealId}', [DishController::class, "getDishByDealId"]);

Route::get('/dishes/getdealsdishes/{dealId}/{restaurantId}', [DishController::class, "getDishByDealId2"]);

Route::get('/dishes/by-dishid/{dishId}', [DishController::class, "getDishById"]);
Route::get('/dishes/by-dishid/{dishId}/{restaurantId}', [DishController::class, "getDishById2"]);

Route::get('/dishes/getusermenu/dealsdishes', [DishController::class, "getAllDishesWithDeals"]);


Route::post('/dishes/multiple/{restaurantId}', [DishController::class, "storeMultipleDish"]);

Route::post('/dishes/multiple/deal/{menuId}', [DishController::class, "storeMultipleDealDish"]);

Route::patch('/dishes/multiple/deal/{menuId}', [DishController::class, "updateMultipleDealDish"]);

Route::patch('/dishes/Edit/multiple', [DishController::class, "updateMultipleDish"]);
Route::patch('/dishes/Updatedish', [DishController::class, "updateDish2"]);
Route::delete('/dishes/{dishId}', [DishController::class, "deleteDish"]);

// #################
// restaurant  Routes
// #################

















Route::get('/restaurant/{restaurantId}', [RestaurantController::class, "getrestaurantById"]);

Route::get('/variation/dish_variation/{dishId}', [VariationController::class, "getAllDishVariation"]);





Route::get('/review-new/get/questions-all/customer', [ReviewNewController::class, "getQuestionAllUnauthorized"]);


Route::get('/review-new/get/questions-all-report/unauthorized', [ReviewNewController::class, "getQuestionAllReportUnauthorized"]);

Route::get('/review-new/get/questions-all-report/guest/unauthorized', [ReviewNewController::class, "getQuestionAllReportGuestUnauthorized"]);




Route::post('/review-new-guest/{restaurantId}', [ReviewNewController::class, "storeReviewByGuest"]);


// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// @@@@@@@@@@@@@@@@@@@@  Protected Routes      @@@@@@@@@@@@@@@@@
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
Route::middleware(['auth:api'])->group(function () {


    Route::get('/menu-dishes-variationtypes-variations', [MenuController::class, "getCombinedDataMDVTV"]);




    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // coupon management section
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    Route::post('/v1.0/coupons', [CouponController::class, "createCoupon"]);
    Route::put('/v1.0/coupons', [CouponController::class, "updateCoupon"]);
    Route::put('/v1.0/coupons/toggle-active', [CouponController::class, "toggleActiveCoupon"]);
    Route::get('/v1.0/coupons/{business_id}/{perPage}', [CouponController::class, "getCoupons"]);
    Route::get('/v1.0/coupons/single/{business_id}/{id}', [CouponController::class, "getCouponById"]);
    Route::delete('/v1.0/coupons/{business_id}/{id}', [CouponController::class, "deleteCouponById"]);
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // coupon management section
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // campaign management section
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    Route::post('/v1.0/campaigns', [CampaignController::class, "createCampaign"]);
    Route::put('/v1.0/campaigns', [CampaignController::class, "updateCampaign"]);
    Route::put('/v1.0/campaigns/toggle-active', [CampaignController::class, "toggleActiveCampaign"]);
    Route::get('/v1.0/campaigns/{business_id}/{perPage}', [CampaignController::class, "getCampaigns"]);
    Route::get('/v1.0/campaigns/single/{business_id}/{id}', [CampaignController::class, "getCampaignById"]);
    Route::delete('/v1.0/campaigns/{business_id}/{id}', [CampaignController::class, "deleteCampaignById"]);

    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // coupon management section
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%





    Route::post('/v1.0/header-image/{restaurant_id}', [OwnerController::class, "createHeaderImage"]);

    Route::post('/v1.0/rating-page-image/{restaurant_id}', [OwnerController::class, "createRatingPageImage"]);

    Route::post('/v1.0/placeholder-image/{restaurant_id}', [OwnerController::class, "createPlaceholderImage"]);

    Route::post('/v1.0/menu-pdf/{restaurant_id}', [OwnerController::class, "uploadMenuPDF"]);


    // #################
    // Auth Routes
    // #################

    Route::patch('/owner/updateuser', [OwnerController::class, "updateUser"]);
    Route::patch('/owner/updateuser/by-user', [OwnerController::class, "updateUserByUser"]);

    Route::patch('/owner/profileimage', [OwnerController::class, "updateImage"]);
    Route::get('/owner/role/getrole', [OwnerController::class, "getRole"]);



    // #################
    // Restaurent Routes
    // #################



    // ********************************************
    // user management section --role
    // ********************************************
    Route::get('/v1.0/initial-role-permissions', [RolesController::class, "getInitialRolePermissions"]);
    Route::get('/v1.0/initial-permissions', [RolesController::class, "getInitialPermissions"]);
    Route::post('/v1.0/roles', [RolesController::class, "createRole"]);
    Route::put('/v1.0/roles', [RolesController::class, "updateRole"]);
    Route::get('/v1.0/roles', [RolesController::class, "getRoles"]);
    Route::get('/v1.0/roles/{id}', [RolesController::class, "getRoleById"]);
    Route::delete('/v1.0/roles/{ids}', [RolesController::class, "deleteRolesByIds"]);
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // end user management section
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%






















    Route::post('/restaurant', [RestaurantController::class, "storeRestaurent"]);
    Route::post('/restaurant/by-owner-id', [RestaurantController::class, "storeRestaurentByOwnerId"]);
    Route::delete('/restaurant/delete/{id}', [RestaurantController::class, "deleteRestaurantByRestaurentId"]);

    Route::delete('/restaurant/delete/force-delete/{email}', [RestaurantController::class, "deleteRestaurantByRestaurentIdForceDelete"]);


    Route::patch('/restaurant/uploadimage/{restaurentId}', [RestaurantController::class, "uploadRestaurentImage"]);
    Route::patch('/restaurant/UpdateResturantDetails/{restaurentId}', [RestaurantController::class, "UpdateResturantDetails"]);







    Route::patch('/restaurant/UpdateResturantStripeDetails/{restaurentId}', [StripeController::class, "UpdateResturantStripeDetails"]);
    Route::get('/restaurant/getResturantStripeDetails/{id}', [StripeController::class, "GetResturantStripeDetails"]);







    Route::patch('/restaurant/UpdateResturantDetails/byadmin/{restaurentId}', [RestaurantController::class, "UpdateResturantDetailsByAdmin"]);

    Route::get('/restaurant', [RestaurantController::class, "getAllRestaurants"]);

    Route::get('/restaurants/{perPage}', [RestaurantController::class, "getRestaurants"]);

    Route::get('/restaurant/RestuarantbyID/{restaurantId}', [RestaurantController::class, "getrestaurantById"]);
    Route::get('/restaurant/Restuarant/tables/{restaurantId}', [RestaurantController::class, "getrestaurantTableByRestaurantId"]);


    // #################
    // leaflet
    ####################
    Route::post('/leaflet/create', [LeafletController::class, "createLeaflet"]);
    Route::put('/leaflet/update', [LeafletController::class, "updateLeaflet"]);

    Route::delete('/leaflet/{restaurant_id}/{id}', [LeafletController::class, "deleteLeafletById"]);

    // #################
    // variation Routes
    // #################


    Route::post('/variation/variation_type', [VariationController::class, "storeVariationType"]);
    Route::delete('/variation/variation_type/{id}', [VariationController::class, "deleteVariationType"]);
    Route::delete('/variation/variation/{id}', [VariationController::class, "deleteVariation"]);


    Route::post('/variation/variation_type/multiple/{restaurantId}', [VariationController::class, "storeMultipleVariationType"]);
    Route::patch('/variation/variation_type/multiple', [VariationController::class, "updateMultipleVariationType"]);
    Route::patch('/variation/variation/multiple', [VariationController::class, "updateMultipleVariation"]);
    Route::patch('/variation/variationtype', [VariationController::class, "updateVariationType"]);

    Route::post('/variation/variationtype', [VariationController::class, "updateVariationType"]);



    Route::post('/variation', [VariationController::class, "storeVariation"]);

    Route::post('/variation/multiple/varations', [VariationController::class, "storeMultipleVariation"]);

    Route::patch('/variation', [VariationController::class, "updateVariation"]);

    Route::post('/variation/dish_variation', [VariationController::class, "storeDishVariation"]);

    Route::post('/variation/multiple/dish_variation/{dishId}', [VariationController::class, "storeMultipleDishVariation"]);



    Route::patch('/variation/dish_variation', [VariationController::class, "updateDishVariation"]);
    Route::patch('/variation/dish_variation/multiple/{dishId}', [VariationController::class, "updateMultipleDishVariation"]);

    Route::get('/variation/{restaurantId}', [VariationController::class, "getAllVariationWithDish"]);
    Route::get('/variation-type/{id}', [VariationController::class, "getSingleVariationType"]);

    Route::get('/variation2/{restaurantId}', [VariationController::class, "getAllVariationTypeWithVariation"]);
    Route::get('/variation2/{restaurantId}/{dishId}', [VariationController::class, "getAllVariationTypeWithVariationByDishId"]);

    Route::get('/variation/type/count/{typeId}', [VariationController::class, "getAllVariationByType_Id"]);

    Route::get('/variation/by-restaurant-id/{restaurant_id}', [VariationController::class, "getAllVariationByRestaurantId"]);


    Route::delete('/variation/unlink/{typeId}/{dishId}', [VariationController::class, "deleteDishVariation"]);

    // #################
    // dailyviews Routes

    // #################
    Route::post('/dailyviews/{restaurantId}', [DailyViewsController::class, "store"]);
    Route::patch('/dailyviews/update/{restaurantId}', [DailyViewsController::class, "update"]);

    // #################
    // forggor password Routes

    // #################

    Route::patch('/auth/changepassword', [ForgotPasswordController::class, "changePassword"]);

    // #################
    // notification  Routes
    // #################

    Route::post('/notification', [NotificationController::class, "storeNotification"]);
    Route::patch('/notification/{notificationId}', [NotificationController::class, "updateNotification"]);
    Route::get('/notification', [NotificationController::class, "getNotification"]);
    Route::delete('/notification/{notificationId}', [NotificationController::class, "deleteNotification"]);






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


    // #################
    // User Dashboard Routes
    // #################



    // #################
    // Super admin  Routes
    // #################




    Route::post('/user-dashboard/create', [DashboardWidgetController::class, "createUserDashboard"]);
    Route::put('/user-dashboard/update', [DashboardWidgetController::class, "updateUserDashboard"]);
    Route::get('/user-dashboard/get', [DashboardWidgetController::class, "getUserDashboard"]);
    Route::get('/user-dashboard/get/{id}', [DashboardWidgetController::class, "getUserDashboardById"]);
    Route::delete('/user-dashboard/delete/{id}', [DashboardWidgetController::class, "deleteUserDashboardById"]);




    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // expense type management section
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    Route::post('/v1.0/suppliers', [SupplierController::class, "createSupplier"]);
    Route::put('/v1.0/suppliers', [SupplierController::class, "updateSupplier"]);
    Route::get('/v1.0/suppliers/{restaurant_id}/{perPage}', [SupplierController::class, "getSuppliers"]);
    Route::get('/v1.0/suppliers/{restaurant_id}', [SupplierController::class, "getAllSuppliers"]);
    Route::delete('/v1.0/suppliers/{id}', [SupplierController::class, "deleteSupplierById"]);
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // expense type management section
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // expense type management section
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    Route::post('/v1.0/expense-types', [ExpenseTypeController::class, "createExpenseType"]);
    Route::put('/v1.0/expense-types', [ExpenseTypeController::class, "updateExpenseType"]);
    Route::get('/v1.0/expense-types/{restaurant_id}/{perPage}', [ExpenseTypeController::class, "getExpenseTypes"]);
    Route::get('/v1.0/expense-types/{restaurant_id}', [ExpenseTypeController::class, "getAllExpenseTypes"]);

    Route::delete('/v1.0/expense-types/{id}', [ExpenseTypeController::class, "deleteExpenseTypeById"]);
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    // expense type management section
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%




    Route::post('/v1.0/expenses', [PaymentController::class, "createInvoicePayment"]);
    Route::put('/v1.0/expenses', [PaymentController::class, "updateInvoicePayment"]);
    Route::get('/v1.0/expenses/{restaurant_id}/{perPage}', [PaymentController::class, "getInvoicePayments"]);
    Route::get('/v2.0/expenses/get/single/{id}', [PaymentController::class, "getInvoicePaymentByIdv2"]);

    Route::delete('/v1.0/expenses/{id}', [PaymentController::class, "deleteInvoicePaymentByIdV2"]);
    Route::post('/v1.0/payments-invoice-file', [PaymentController::class, "createPaymentInvoiceFile"]);

    Route::get('/restaurant-partner/get', [RestaurantPartnerController::class, "getRestaurantPartner"]);




    Route::get('/v1.0/customers', [CustomerController::class, "getCustomers"]);






    Route::middleware(['superadmin'])->group(function () {







        Route::patch('/superadmin/auth/changepassword', [ForgotPasswordController::class, "changePasswordBySuperAdmin"]);

        Route::patch('/superadmin/auth/change-email', [ForgotPasswordController::class, "changeEmailBySuperAdmin"]);



        Route::post('/superadmin/dashboard-widget/create', [DashboardWidgetController::class, "createWidget"]);
        Route::put('/superadmin/dashboard-widget/update', [DashboardWidgetController::class, "updateWidget"]);
        Route::get('/superadmin/dashboard-widget/get', [DashboardWidgetController::class, "getWidget"]);
        Route::get('/superadmin/dashboard-widget/get/{id}', [DashboardWidgetController::class, "getWidgetById"]);
        Route::delete('/superadmin/dashboard-widget/delete/{id}', [DashboardWidgetController::class, "deleteWidgetById"]);




        Route::post('/superadmin/restaurant-partner/create', [RestaurantPartnerController::class, "createRestaurantPartner"]);
        Route::put('/superadmin/restaurant-partner/update', [RestaurantPartnerController::class, "updateRestaurantPartner"]);
        Route::get('/superadmin/restaurant-partner/get', [RestaurantPartnerController::class, "getRestaurantPartnerSuperAdmin"]);
        Route::get('/superadmin/restaurant-partner/get/{id}', [RestaurantPartnerController::class, "getRestaurantPartnerById"]);
        Route::delete('/superadmin/restaurant-partner/delete/{id}', [RestaurantPartnerController::class, "deleteRestaurantPartnerById"]);

        Route::get('/superadmin/dashboard-report/total-restaurant', [SuperAdminReportController::class, "getTotalRestaurantReport"]);


        Route::get('/superadmin/dashboard-report/total-restaurant-enabled', [SuperAdminReportController::class, "getTotalEnabledRestaurantReport"]);
        Route::get('/superadmin/dashboard-report/total-restaurant-disabled', [SuperAdminReportController::class, "getTotalDisabledRestaurantReport"]);
        Route::get('/superadmin/dashboard-report/total-customers', [SuperAdminReportController::class, "getTotalCustomers"]);
        Route::get('/superadmin/dashboard-report/total-customers', [SuperAdminReportController::class, "getTotalCustomers"]);




        Route::get('/superadmin/dashboard-report/monthly-orders', [SuperAdminReportController::class, "getMonthlyOrders"]);
        Route::get('/superadmin/dashboard-report/monthly-revenue', [SuperAdminReportController::class, "getMonthlyRevenue"]);
        Route::get('/superadmin/dashboard-report/monthly-customer', [SuperAdminReportController::class, "getMonthlyCustomer"]);
        Route::get('/superadmin/dashboard-report/monthly-menu', [SuperAdminReportController::class, "getMonthlyMenu"]);


        Route::get('/superadmin/dashboard-report/weekly-orders', [SuperAdminReportController::class, "getWeeklyOrders"]);
        Route::get('/superadmin/dashboard-report/weekly-revenue', [SuperAdminReportController::class, "getWeeklyRevenue"]);
        Route::get('/superadmin/dashboard-report/weekly-customer', [SuperAdminReportController::class, "getWeeklyCustomer"]);
        Route::get('/superadmin/dashboard-report/weekly-menu', [SuperAdminReportController::class, "getWeeklyMenu"]);










        Route::get('/superadmin/dashboard-report/total-orders', [SuperAdminReportController::class, "getTotalOrders"]);
        Route::get('/superadmin/dashboard-report/today-orders', [SuperAdminReportController::class, "getTodayOrders"]);
        Route::get('/superadmin/dashboard-report/total-reviews', [SuperAdminReportController::class, "getTotalReviews"]);
        Route::get('/superadmin/dashboard-report/today-reviews', [SuperAdminReportController::class, "getTodayReviews"]);
        Route::get('/superadmin/dashboard-report/review-report', [SuperAdminReportController::class, "getReviewReport"]);
        Route::get('/superadmin/dashboard-report/order-report', [SuperAdminReportController::class, "getSuperAdminOrderReport"]);
        Route::get('/superadmin/dashboard-report/customer-report', [SuperAdminReportController::class, "getCustomerReport"]);




        Route::get('/superadmin/customer-list/{perPage}', [UserController::class, "getCustomerReportSuperadmin"]);

        Route::get('/superadmin/owner-list/{perPage}', [UserController::class, "getOwnerReport"]);

        Route::delete('/superadmin/user-delete/{id}', [UserController::class, "deleteCustomerById"]);




        Route::put('/v1.0/email-template-wrappers', [EmailTemplateWrapperController::class, "updateEmailTemplateWrapper"]);
        Route::get('/v1.0/email-template-wrappers/{perPage}', [EmailTemplateWrapperController::class, "getEmailTemplateWrappers"]);
        Route::get('/v1.0/email-template-wrappers/single/{id}', [EmailTemplateWrapperController::class, "getEmailTemplateWrapperById"]);





        Route::post('/v1.0/email-templates', [EmailTemplateController::class, "createEmailTemplate"]);
        Route::put('/v1.0/email-templates', [EmailTemplateController::class, "updateEmailTemplate"]);
        Route::get('/v1.0/email-templates/{perPage}', [EmailTemplateController::class, "getEmailTemplates"]);
        Route::get('/v1.0/email-template-types', [EmailTemplateController::class, "getEmailTemplateTypes"]);
        Route::delete('/v1.0/email-templates/{id}', [EmailTemplateController::class, "deleteEmailTemplateById"]);
        Route::get('/v1.0/email-templates/single/{id}', [EmailTemplateController::class, "getEmailTemplateById"]);
    });






    // #################
    // Report Routes
    // #################




    Route::get('/customer-dish-report/by-phone-number/{phone}/{restaurantId}', [ReportController::class, "customerDishReport"]);

    Route::get('/customer-dish-report/by-customer-id/{customer_id}/{restaurantId}', [ReportController::class, "customerDishReportByCustomerId"]);






    Route::get('/customer-report', [ReportController::class, "customerDashboardReport"]);
    Route::get('/restaurant-report', [ReportController::class, "restaurantDashboardReport"]);



    Route::get('/v1.0/business-owner-dashboard', [DashboardManagementController::class, "getBusinessOwnerDashboardData"]);

    Route::get('/v1.0/sales-reports', [DashboardManagementController::class, "getSalesReports"]);





    Route::get('/dashboard-report/get/table-report/{restaurantId}', [ReportController::class, "getTableReport"]);




    Route::get('/dashboard-report/{restaurantId}', [ReportController::class, "getDashboardReport"]);

    Route::get('/dashboard-report2', [ReportController::class, "getDashboardReport2"]);
    Route::get('/dashboard-report/restaurant/get', [ReportController::class, "getRestaurantReport"]);

    // #################
    // Review Owner Routes
    // #################

    Route::post('/review-new/owner/create/questions', [ReviewNewController::class, "storeOwnerQuestion"]);
    Route::patch('/review-new/owner/update/questions', [ReviewNewController::class, "updateOwnerQuestion"]);
    // #################
    // order Routes
    // #################




    Route::post('/order/{restaurantId}', [OrderController::class, "storeOrder"]);


    Route::post('/order/orderbyuser/{restaurantId}', [OrderController::class, "storeByUser"]);
    Route::patch('/order/{orderId}', [OrderController::class, "orderComplete"]);
    Route::patch('/order/updatestatus/{orderId}', [OrderController::class, "updateStatus"]);
    Route::patch('/order/edit/order/{orderId}', [OrderController::class, "editOrder"]);



    Route::delete('/order/{orderId}', [OrderController::class, "deleteOrder"]);
    Route::get('/order/{orderId}', [OrderController::class, "getOrderById"]);
    Route::get('/order/by-restaurant/{orderId}/{restaurantId}', [OrderController::class, "getOrderById2"]);

    Route::get('/order/getorderby/customerid/{customerId}', [OrderController::class, "getOrderByCustomerId"]);



    Route::get('/order/orderlist/today/{status}', [OrderController::class, "getTodaysOrderByStatus"]);
    Route::get('/order/category/{orderId}', [OrderController::class, "getOrderById"]);
    Route::get('/order/All/order/dishes/{orderId}', [OrderController::class, "getOrderById"]);

    Route::get('/order/All/order', [OrderController::class, "getAllOrder"]);


    Route::get('/order/All/order/today/{restaurantId}', [OrderController::class, "getAllOrderToday"]);



    Route::get('/order/All/order/every/{perPage}/{restaurantId}', [OrderController::class, "getAllOrderEveryDay"]);

    Route::get('/order/All/order/every/{restaurantId}', [OrderController::class, "getAllOrderEveryDayV2"]);

    Route::get('/v3.0/order/All/order/every/{restaurantId}', [OrderController::class, "getAllOrderEveryDayV3"]);



    Route::get('/order/All/order/by-date/{restaurantId}', [OrderController::class, "getAllOrder"]);

    Route::get('/order/All/order-by-type/{type}', [OrderController::class, "getOrderByType"]);

    Route::get('/order/All/order-by-type/by-restaurant/{type}/{restaurantId}', [OrderController::class, "getOrderByType2"]);


    Route::get('/order/All/pending/order/{restaurantId}', [OrderController::class, "getAllPendingOrder"]);
    Route::get('/order/All/pending/order/{restaurantId}/{perPage}', [OrderController::class, "getAllPendingOrderWithPagination"]);


    Route::get('/order/All/autoprint/order/{restaurantId}', [OrderController::class, "getAllAutoPrintOrder"]);

    Route::get('/order/get/daily/order/report', [OrderController::class, "getdailyOrderReport"]);
    Route::get('/order/oderreporting/{min}/{max}/{fromdate}/{todate}/{status}', [OrderController::class, "getorderReport"]);
    Route::get('/order/oderreporting/{restaurantId}/{min}/{max}/{fromdate}/{todate}/{status}', [OrderController::class, "getorderReportByRestaurantId"]);
    Route::get('/order/oderreporting/{restaurantId}/{fromdate}/{todate}/{status}', [OrderController::class, "getorderReportByRestaurantId2"]);
    Route::get('/order/byuser/all/order', [OrderController::class, "getOrderByUser"]);
});

// #################
// forggor password Routes
// #################

Route::post('/forgetpassword', [ForgotPasswordController::class, "storeForgetPassword"]);
Route::patch('/forgetpassword/reset/{token}', [ForgotPasswordController::class, "changePasswordByToken"]);
Route::get('/order/get/table-information/{restaurantId}', [OrderController::class, "getTableInformation"]);
Route::post('webhooks/stripe', [CustomWebhookController::class, "handleStripeWebhook"]);
Route::get('/client/restaurants/{perPage}', [RestaurantController::class, "getRestaurantsClients"]);

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// coupon management section
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


Route::get('/v1.0/client/coupons/by-business-id/{business_id}/{perPage}', [ClientCouponController::class, "getCouponsByBusinessIdClient"]);
Route::get('/v1.0/client/coupons/all/{perPage}', [ClientCouponController::class, "getCouponsClient"]);

Route::get('/v1.0/client/coupons/single/{id}', [ClientCouponController::class, "getCouponByIdClient"]);

Route::get('/v1.0/client/coupons/get-discount/{business_id}/{code}/{amount}', [ClientCouponController::class, "getCouponDiscountClient"]);

Route::get('/v1.0/client/coupons/all-auto-applied-coupons/{business_id}', [ClientCouponController::class, "getAutoAppliedCouponsByBusinessIdClient"]);



// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// coupon management section
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

Route::get('/v1.0/client/campaigns/{business_id}/{perPage}', [CampaignController::class, "getCampaignsClient"]);
 Route::get('/client/restaurant/getResturantStripeDetails/{id}', [StripeController::class, "GetResturantStripeDetailsClient"]);
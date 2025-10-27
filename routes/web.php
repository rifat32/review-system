<?php


use App\Http\Controllers\SetupController;

use App\Http\Controllers\SwaggerLoginController;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/swagger-refresh', function () {
    Artisan::call('l5-swagger:generate');
    return "swagger generated";
});


Route::get('/passport', function () {

    ini_set('memory_limit', '512M');

    Artisan::call('passport:install');
    return "passport";
});


Route::get('/pdf', function () {
    Artisan::call('guest_user_review_report:generate');
    Artisan::call('user_review_report:generate');
    return "pdf generated";
});



Route::get('/migrate', [SetUpController::class, "migrate"]);
Route::get("/setup", [SetupController::class, "setup"]);



Route::get('/', function () {
    return view('welcome');
});

Route::get("/swagger-login", [SwaggerLoginController::class, "login"])->name("login.view");
Route::post("/swagger-login", [SwaggerLoginController::class, "passUser"]);
















Route::get(
    "/query",
    function () {
        // Restaurant::where([
        //     "id" => 251
        // ])
        // ->update([
        //     "eat_in_payment_mode" => [
        //         "cash" => 1,
        //         "stripe" => 0
        //     ],
        //     "takeaway_payment_mode" => [
        //         "cash" => 1,
        //         "stripe" => 0
        //     ],
        //     "delivery_payment_mode" => [
        //         "cash" => 1,
        //         "stripe" => 0
        //     ]
        //     ]);


        //   $dishes =  Dish::where(function($query)  {
        //         $query->where([
        //             "restaurant_id" => 251
        //         ])
        //         ->orWhereHas("menu", function($query) {
        //             $query->where([
        //                 "menus.restaurant_id" => 251
        //             ]);
        //         });

        //     })
        //     ->get();

        //     $dishes->each(function($dish) {

        //         $dish->take_away =   $dish->price;
        //         $dish->delivery =   $dish->price;
        //         $dish->save();


        //     });

        return "query run";
    }

);

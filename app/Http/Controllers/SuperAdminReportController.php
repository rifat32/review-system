<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\ReviewValueNew;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SuperAdminReportController extends Controller
{
/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-restaurant",
     *      operationId="getTotalRestaurantReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get TotalRestaurant report",
     *      description="This method is to get TotalRestaurant report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */


    public function getTotalRestaurantReport (Request $request) {
        $data["data"] = Restaurant::get()->count();
        return response()->json($data,200);
    }


    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-restaurant-enabled",
     *      operationId="getTotalEnabledRestaurantReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get TotalEnabled restaurant report",
     *      description="This method is to get TotalEnabled  restaurant report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */

    public function getTotalEnabledRestaurantReport (Request $request) {
        $data["data"] = Restaurant::where("expiry_date",">", now())->get()->count();
        return response()->json($data,200);
    }


    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-restaurant-disabled",
     *      operationId="getTotalDisabledRestaurantReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Total Disabledrestaurant report",
     *      description="This method is to get Total Disabled restaurant report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */


    public function getTotalDisabledRestaurantReport (Request $request) {
        $data["data"] = Restaurant::where("expiry_date",">", now())->get()->count();
        return response()->json($data,200);
    }

  /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-customers",
     *      operationId="getTotalCustomers",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Total Customers report",
     *      description="This method is to get Total Customers  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */


    public function getTotalCustomers (Request $request) {
        $data["data"] = Order::select("customer_id")
            ->distinct()
            ->get()->count();
        return response()->json($data,200);
    }

/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-orders",
     *      operationId="getTotalOrders",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Total Orders report",
     *      description="This method is to get Total Orders  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getTotalOrders (Request $request) {
        $data["data"] = Order::get()->count();
        return response()->json($data,200);
    }





/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/monthly-orders",
     *      operationId="getMonthlyOrders",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get monthly Orders report",
     *      description="This method is to get monthly Orders  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getMonthlyOrders (Request $request) {
        $data["this_month"] = Order::where('created_at', '>', now()->subDays(30)->endOfDay())->get()->count();
        $data["previous_month"] = Order::whereBetween(
            'created_at',
            [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()]
        )->get()->count();


        for ($i = 0; $i <= 29; $i++) {
            $revenueTotalAmount = Order::whereDate('created_at', Carbon::today()->subDay($i))->get()->count();

            $data["data"][$i]["total"] =  $revenueTotalAmount;
            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }


    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/monthly-revenue",
     *      operationId="getMonthlyRevenue",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get monthly Orders report",
     *      description="This method is to get monthly Orders  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getMonthlyRevenue (Request $request) {

        $data["this_month_cash"] = Order::where('created_at', '>', now()->subDays(30)->endOfDay())->sum("cash");
        $data["this_month_card"] = Order::where('created_at', '>', now()->subDays(30)->endOfDay())->sum("card");
        $data["previous_month_cash"] = Order::whereBetween(
            'created_at',
            [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()]
        )->sum("cash");
        $data["previous_month_card"] = Order::whereBetween(
            'created_at',
            [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()]
        )->sum("card");
        $data["this_month"] = ($data["this_month_cash"] * 1) + ($data["this_month_card"] * 1);
        $data["previous_month"] = ($data["previous_month_cash"] * 1) + ($data["previous_month_card"] * 1);





        for ($i = 0; $i <= 29; $i++) {
            $cash = Order::whereDate('created_at', Carbon::today()->subDay($i))->sum("cash");
            $card = Order::whereDate('created_at', Carbon::today()->subDay($i))->sum("card");
            $data["data"][$i]["cash"] =  $cash;
            $data["data"][$i]["card"] =  $card;
            $data["data"][$i]["total"] =  ($card * 1) + ($cash * 1);
            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }

 /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/monthly-customer",
     *      operationId="getMonthlyCustomer",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get monthly Customer report",
     *      description="This method is to get monthly Customer  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getMonthlyCustomer (Request $request) {
        $data["this_month"] = Order::where('created_at', '>', now()->subDays(30)->endOfDay()) ->select("customer_id")
        ->distinct()->get()->count();
        $data["previous_month"] = Order::whereBetween(
            'created_at',
            [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()]
        ) ->select("customer_id")
        ->distinct()->get()->count();



        for ($i = 0; $i <= 29; $i++) {
            $customer = Order::whereDate('created_at', Carbon::today()->subDay($i))
            ->select("customer_id")
            ->distinct()
            ->get()->count();

            $data["data"][$i]["total"] =  $customer;


            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }


/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/monthly-menu",
     *      operationId="getMonthlyMenu",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get monthly Menu report",
     *      description="This method is to get monthly Menu  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getMonthlyMenu (Request $request) {
        $data["this_month"] = Menu::where('created_at', '>', now()->subDays(30)->endOfDay())->get()->count();
        $data["previous_month"] = Menu::whereBetween(
            'created_at',
            [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()]
        )->get()->count();

        for ($i = 0; $i <= 29; $i++) {
            $customer = Menu::whereDate('created_at', Carbon::today()->subDay($i))->get()->count();

            $data["data"][$i]["total"] =  $customer;


            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }































/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/weekly-orders",
     *      operationId="getWeeklyOrders",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get weekly Orders report",
     *      description="This method is to get weekly Orders  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getWeeklyOrders (Request $request) {
        $data["this_week"] = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get()->count();
        $data["previous_week"] = Order::whereBetween(
            'created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->get()->count();
        for ($i = 0; $i <= 6; $i++) {
            $revenueTotalAmount = Order::whereDate('created_at', Carbon::today()->subDay($i))->get()->count();

            $data["data"][$i]["total"] =  $revenueTotalAmount;
            $data["data"][$i]["date"] = date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }


    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/weekly-revenue",
     *      operationId="getWeeklyRevenue",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Weekly revenue report",
     *      description="This method is to get Weekly revenue  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getWeeklyRevenue (Request $request) {
        $data["this_week_cash"] = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum("cash");
        $data["this_week_card"] = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum("card");
        $data["previous_week_cash"] = Order::whereBetween(
            'created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->sum("cash");
        $data["previous_week_card"] = Order::whereBetween(
            'created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->sum("card");
        $data["this_week"] = ($data["this_week_cash"] * 1) + ($data["this_week_card"] * 1);
        $data["previous_week"] = ($data["previous_week_cash"] * 1) + ($data["previous_week_card"] * 1);




        for ($i = 0; $i <= 6; $i++) {
            $cash = Order::whereDate('created_at', Carbon::today()->subDay($i))->sum("cash");
            $card = Order::whereDate('created_at', Carbon::today()->subDay($i))->sum("card");
            $data["data"][$i]["cash"] =  $cash;
            $data["data"][$i]["card"] =  $card;
            $data["data"][$i]["total"] =  ($card * 1) + ($cash * 1);
            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }

 /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/weekly-customer",
     *      operationId="getWeeklyCustomer",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get monthly Customer report",
     *      description="This method is to get monthly Customer  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getWeeklyCustomer (Request $request) {

        $data["this_week"] = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]) ->select("customer_id")
        ->distinct()->get()->count();
        $data["previous_week"] = Order::whereBetween(
            'created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->select("customer_id")
        ->distinct()->get()->count();
        for ($i = 0; $i <= 6; $i++) {
            $customer = Order::whereDate('created_at', Carbon::today()->subDay($i)) ->select("customer_id")
            ->distinct()->get()->count();

            $data["data"][$i]["total"] =  $customer;


            $data["data"][$i]["date"] =  date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }


/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/weekly-menu",
     *      operationId="getWeeklyMenu",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get weekly menu report",
     *      description="This method is to get weekly menu  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getWeeklyMenu (Request $request) {
        $data["this_week"] = Menu::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get()->count();
        $data["previous_week"] = Menu::whereBetween(
            'created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->get()->count();
        for ($i = 0; $i <= 6; $i++) {
            $customer = Menu::whereDate('created_at', Carbon::today()->subDay($i))->get()->count();

            $data["data"][$i]["total"] =  $customer;


            $data["data"][$i]["date"] = date_format(Carbon::today()->subDay($i),"d/m/Y");

        }
        return response()->json($data, 200);


    }


















/**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/today-orders",
     *      operationId="getTodayOrders",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Today Orders report",
     *      description="This method is to Today Orders  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */




    public function getTodayOrders (Request $request) {
        $data["data"] = Order::whereDate('created_at', Carbon::today())->get()->count();
        return response()->json($data,200);
    }
    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/total-reviews",
     *      operationId="getTotalReviews",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Total Reviews report",
     *      description="This method is to Total Reviews  report",


     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */


    public function getTotalReviews (Request $request) {
        $data["data"] = ReviewValueNew::get()->count();
        return response()->json($data,200);
    }

    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/today-reviews",
     *      operationId="getTodayReviews",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get Today Reviews report",
     *      description="This method is to Today Reviews  report",

     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */




    public function getTodayReviews (Request $request) {
        $data["data"] = ReviewValueNew::whereDate('created_at', Carbon::today())->get()->count();
        return response()->json($data,200);
    }

    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/review-report",
     *      operationId="getReviewReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get  Reviews report",
     *      description="This method is to get Reviews  report",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */



    public function getReviewReport (Request $request) {
        $data["total_reviews"] = ReviewValueNew::get()->count();


    $data["previous_week_total_reviews"] = ReviewValueNew::
        whereBetween(
            'review_value_news.created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )
        ->get()->count();


    $data["this_week_total_reviews"] = ReviewValueNew::whereBetween('review_value_news.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->get()->count();
        return response()->json($data,200);
    }



  /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/order-report",
     *      operationId="getSuperAdminOrderReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get  Order report",
     *      description="This method is to get Order  report",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */




    public function getSuperAdminOrderReport (Request $request) {
        $data["total_orders"] = Order::get()->count();


    $data["previous_week_total_orders"] = Order::
        whereBetween(
            'orders.created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )
        ->get()->count();


    $data["this_week_total_orders"] = Order::whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->get()->count();
        return response()->json($data,200);
    }


    /**
     *
     * @OA\Get(
     *      path="/superadmin/dashboard-report/customer-report",
     *      operationId="getCustomerReport",
     *      tags={"super_admin_report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get  Customer report",
     *      description="This method is to get Customer  report",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     * @OA\JsonContent(),
     *      ),
     *        @OA\Response(
     *          response=422,
     *          description="Unprocesseble Content",
     *    @OA\JsonContent(),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *@OA\JsonContent()
     *      )
     *     )
     */
    public function getCustomerReport (Request $request) {
        $data["total_customers"] = Order::select("customer_id")
        ->distinct()->get()->count();


    $data["previous_week_total_customers"] = Order::select("customer_id")
       ->distinct()
        ->whereBetween(
            'orders.created_at',
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )
        ->get()->count();


    $data["this_week_total_customers"] = Order::select("customer_id")
    ->distinct()
     ->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->get()->count();
        return response()->json($data,200);
    }



}

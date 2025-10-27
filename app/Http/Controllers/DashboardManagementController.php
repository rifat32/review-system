<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorUtil;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardManagementController extends Controller
{
    use ErrorUtil;

    /**
     *
     * @OA\Get(
     *      path="/v1.0/business-owner-dashboard",
     *      operationId="getBusinessOwnerDashboardData",
     *      tags={"reports"},
     *       security={
     *           {"bearerAuth": {}}
     *       },

     * @OA\Parameter(
     *     name="customer_date_filter",
     *     in="query",
     *     description="Customer date filter",
     *     required=true,
     *     example=""
     * ),

     * @OA\Parameter(
     *     name="order_date_filter",
     *     in="query",
     *     description="Booking date filter",
     *     required=true,
     *     example=""
     * ),


     *
     * @OA\Parameter(
     *     name="revenue_date_filter",
     *     in="query",
     *     description="Revenue date filter",
     *     required=true,
     *     example=""
     * ),
     * * @OA\Parameter(
     *     name="order_distribution_date",
     *     in="query",
     *     description="Top services date filter",
     *     required=true,
     *     example=""
     * ),


     *    * *  * * @OA\Parameter(
     *     name="order_date_filter_date_wise",
     *     in="query",
     *     description="Top services date filter",
     *     required=true,
     *     example=""
     * ),
     *
     *
     * @OA\Parameter(
     *     name="top_dishes_date_filter",
     *     in="query",
     *     description="Top services date filter",
     *     required=true,
     *     example=""
     * ),

     *
     *      summary="get all dashboard data combined",
     *      description="get all dashboard data combined",
     *

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
     *   @OA\JsonContent()
     * ),
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request",
     *   *@OA\JsonContent()
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found",
     *   *@OA\JsonContent()
     *   )
     *      )
     *     )
     */

    public function getBusinessOwnerDashboardData(Request $request)
    {
        try {

            $this->storeActivity($request, "");

            $restaurant = Restaurant::where([
                "OwnerID" => auth()->user()->id
            ])
                ->first();

            if (empty($restaurant)) {
                return response()->json([
                    "message" => "You are not a business user"
                ], 401);
            }

            // Define validation rules for date filters
            $validator = Validator::make($request->all(), [
                'customer_date_filter' => 'required|string',

                'order_date_filter' => 'required|string',
                'order_date_filter_date_wise' => 'required|string',

                'revenue_date_filter' => 'required|string',

                'top_dishes_date_filter' => 'required|string',
                'order_distribution_date' => 'required|string',

            ], [
                '*.required' => 'The :attribute field is required.',
                '*.string' => 'The :attribute must be a valid string.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Call the method with different time periods
            $data["customers"] = $this->getCustomersByPeriod(request()->input("customer_date_filter"), $restaurant);

            $data["orders"] = $this->ordersByStatusCount(request()->input("order_date_filter"), $restaurant);

            $data["orders_date_wise"] = $this->ordersByStatusCount(request()->input("order_date_filter_date_wise"), $restaurant);

            $data["revenue"] = $this->revenue(request()->input("revenue_date_filter"), $restaurant);

            $data["top_dishes"] = $this->getTopDishes(request()->input("top_dishes_date_filter"), $restaurant);

            $data["order_distribution"] = $this->getOrderDistribution(request()->input("order_distribution_date"), $restaurant);


            $data["order_status"] = [
                "completed" => $this->ordersByStatusCount("completed", $restaurant),
                "pending" => $this->ordersByStatusCount("pending", $restaurant),
                "canceled" => $this->ordersByStatusCount("canceled", $restaurant),
                "payment_status" => [
                    "paid" => Order::where("payment_status", "paid")->where("restaurant_id", $restaurant->id)->count(),
                    "unpaid" => Order::where("payment_status", "unpaid")->where("restaurant_id", $restaurant->id)->count()
                ],
                "order_fulfillment_times" => Order::where("restaurant_id", $restaurant->id)
                    ->selectRaw("AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_fulfillment_time")
                    ->value('avg_fulfillment_time')
            ];

            $data["customer_report"] = [
                "customers" => $this->getCustomersByPeriod(request()->input("customer_date_filter"), $restaurant),
                "last_order" => Order::where("restaurant_id", $restaurant->id)
                    ->orderBy("created_at", "desc")
                    ->first(["created_at"]),
            ];

            $data["filters"] = [
                "customers" => request()->input("customer_filters"),
                "orders" => request()->input("order_filters"),
            ];




            return response()->json($data, 200);
        } catch (Exception $e) {
            return $this->sendError($e, 500, $request);
        }
    }


    public function getCustomersByPeriod($period, $restaurant)
    {

        $dateRange = $this->getDateRange($period);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $first_time_customers = User::whereHas('orders', function ($query) use ($restaurant, $start, $end) {
            $query->where("orders.restaurant_id", $restaurant->id)
                ->whereRaw('(SELECT COUNT(*) FROM orders o WHERE o.customer_id = orders.customer_id AND o.restaurant_id = ?) = 1', [$restaurant->id])
                ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
                    $query->whereBetween('orders.created_at', [$start, $end]);
                })
            ;
        })

            ->distinct()
            ->get();

        $returning_customers = User::whereHas('orders', function ($query) use ($restaurant, $start, $end) {
            $query->where("orders.restaurant_id", $restaurant->id)
                ->whereRaw('(SELECT COUNT(*) FROM orders o WHERE o.customer_id = orders.customer_id AND o.restaurant_id = ?) > 1', [$restaurant->id])
                ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
                    $query->whereBetween('orders.created_at', [$start, $end]);
                });
        })

            ->distinct()
            ->get();

        // Return the results
        return [
            'first_time_customers' => $first_time_customers,
            'returning_customers' => $returning_customers,
        ];
    }


    public function ordersByStatusCount($range = 'today', $restaurant)
    {
        $statuses = [
            "all",
            "completed",
            "pending",
            "delivery",
            "eat_in",
            "take_away"
        ];

        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = $this->orders($range)

                ->where("restaurant_id", $restaurant->id)
                ->when($status != "all", function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->count();
        }

        return $counts;
    }


    public function orders($range = 'today')
    {
        $dateRange = $this->getDateRange($range);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        return Order::with([

            "user" => function ($query) {
                $query->select(
                    "id",
                    'first_Name',
                    'last_Name'
                );
            },
        ])


            ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
                $query->whereBetween('orders.created_at', [$start, $end]);
            })
            ->when(request()->filled("start_date") && request()->filled("end_date"), function ($query) {
                $query->whereBetween('orders.created_at', [request()->start_date, request()->end_date]);
            })
        ;
    }


    public function revenue($range = 'today', $restaurant)
    {
        // Fetch payments and sum the amount
        return [

            "app_customer_revenue" => $this->calculateRevenue($range, $restaurant),

            "walk_in_customer_revenue" => $this->calculateRevenue($range, $restaurant),

        ];
    }
    protected function calculateRevenue($range, $restaurant)
    {
        $dateRange = $this->getDateRange($range);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        return Order::where("status", "completed")
             ->where("payment_status", "unpaid")
            ->when(request()->filled("payment_type"), function ($query) {
                $payment_typeArray = explode(',', request()->payment_type);
                $query->whereIn("orders.payment_method", $payment_typeArray);
            })


            ->when(request()->filled('is_returning_customers'), function ($query) {
                $isReturning = request()->boolean("is_returning_customers");

                // Separate subquery to count all bookings for each customer.
                $query->whereIn('orders.customer_id', function ($subquery) use ($isReturning) {
                    $subquery->select('customer_id')
                        ->from('orders')
                        ->groupBy('customer_id')
                        ->having(DB::raw('COUNT(orders.id)'), $isReturning ? '>' : '=', 1);
                });
            })

            ->where('orders.restaurant_id', $restaurant->id)


            ->when(!empty(request()->dish_ids), function ($query) {
                $dish_ids = explode(',', request()->dish_ids);

                return $query->whereHas('detail', function ($query) use ($dish_ids) {


                    $query->whereIn('order_details.dish_id', $dish_ids)
                        ->orWhereIn('order_details.meal_id', $dish_ids);


                    // ->when(!empty(request()->service_ids), function ($query) {
                    //     $service_ids = explode(',', request()->service_ids);

                    //     return $query->whereHas('service', function ($query) use ($service_ids) {
                    //         return $query->whereIn('services.id', $service_ids);
                    //     });
                    // });
                });
            })
            ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
                $query->whereBetween('orders.created_at', [$start, $end]);
            })
            ->when((empty($start) && empty($end) && request()->filled("start_date") && request()->filled("end_date")), function ($query) {
                $query->whereBetween('orders.created_at', [request()->start_date, request()->end_date]);
            })
            ->sum('amount');
    }




    public function getTopDishes($range, $restaurant, $limit = true)
{
    $dateRange = $this->getDateRange($range);
    $start = $dateRange['start'];
    $end = $dateRange['end'];

    $top_services = Order::where('orders.restaurant_id', $restaurant->id)
        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->join('dishes', 'order_details.dish_id', '=', 'dishes.id')
        ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
            $query->whereBetween('orders.created_at', [$start, $end]);
        })
        ->selectRaw('dishes.name, COUNT(order_details.dish_id) as dish_count')
        ->groupBy('dishes.name')
        ->orderByDesc('dish_count')
        ->when($limit, function($query) {
            $query->limit(5);
        })
        ->pluck('dishes.name'); // This returns a collection of dish names directly

    return $top_services; // No need to call get() again
}


    public function getOrderDistribution($range, $restaurant)
    {
        $dateRange = $this->getDateRange($range);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $orderTypes = ['pos', 'client'];

        $data = collect($orderTypes)->mapWithKeys(function ($orderType) use ($start, $end, $restaurant) {
            return [
                $orderType => Order::where('order_app', $orderType)
                    ->where('restaurant_id', $restaurant->id)
                    ->when((!empty($start) && !empty($end)), function ($query) use ($start, $end) {
                        $query->whereBetween('orders.created_at', [$start, $end]);
                    })
                    ->count()
            ];
        })->toArray();

        return  $data;
    }








































































    /**
 * @OA\Get(
 *      path="/v1.0/sales-reports",
 *      operationId="getSalesReports",
 *      tags={"reports"},
 *      security={
 *           {"bearerAuth": {}}
 *      },
 *      @OA\Parameter(
 *          name="sales_date_filter",
 *          in="query",
 *          description="Sales date filter",
 *          required=true,
 *          example="this_month"
 *      ),
 *      @OA\Parameter(
 *          name="payment_method_filter",
 *          in="query",
 *          description="Filter by payment method (cash, credit, digital wallets)",
 *          required=false,
 *          example="cash"
 *      ),
 *      @OA\Parameter(
 *          name="category_filter",
 *          in="query",
 *          description="Filter by menu category (Appetizers, Entrées, Desserts)",
 *          required=false,
 *          example="Appetizers"
 *      ),
 *      summary="Get sales report data",
 *      description="Returns detailed sales report including daily, weekly/monthly trends, and sales breakdown by category and item.",
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent()
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *          @OA\JsonContent()
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *          @OA\JsonContent()
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *          @OA\JsonContent()
 *      )
 * )
 */
public function getSalesReports(Request $request)
{
    try {
        $this->storeActivity($request, "");

        // Validate the date filter parameter
        $validator = Validator::make($request->all(), [
            'sales_date_filter' => 'required|string',
            'payment_method_filter' => 'nullable|string',
            'category_filter' => 'nullable|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the restaurant based on the authenticated user
        $restaurant = Restaurant::where([
            "OwnerID" => auth()->user()->id
        ])->first();

        if (empty($restaurant)) {
            return response()->json([
                "message" => "You are not a business user"
            ], 401);
        }

        // Get the date range for the sales filter
        $dateRange = $this->getDateRange($request->input('sales_date_filter'));
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Fetch daily sales summary report
        $daily_sales_summary = $this->getDailySalesSummary(Carbon::today(), $restaurant);


        // Fetch weekly/monthly sales trends
        $sales_trends = $this->getSalesTrends($start, $end, $restaurant);

        // Fetch sales by category & item
        $sales_by_category_item = $this->getSalesByCategoryItem($start, $end, $restaurant, $request->input('category_filter'));

        // Return the report data
        return response()->json([
            'daily_sales_summary' => $daily_sales_summary,
            'sales_trends' => $sales_trends,
            'sales_by_category_item' => $sales_by_category_item
        ], 200);

    } catch (Exception $e) {
        return $this->sendError($e, 500, $request);
    }
}

private function getDailySalesSummary($date, $restaurant)
{
    // Fetch orders for a specific day
    $orders = Order::where('restaurant_id', $restaurant->id)
        ->whereDate('created_at', $date) // Use a specific date for daily sales
        ->get();

    $total_sales = $orders->sum('amount');
    $number_of_transactions = $orders->count();
    $average_order_value = $number_of_transactions > 0 ? $total_sales / $number_of_transactions : 0;

    // Sales by payment method
    $sales_by_payment_method = Order::selectRaw('payment_method, SUM(amount) as total_amount')
        ->where('restaurant_id', $restaurant->id)
        ->whereDate('created_at', $date) // Filter by specific date
        ->groupBy('payment_method')
        ->get();

    return [
        'total_sales' => $total_sales,
        'number_of_transactions' => $number_of_transactions,
        'average_order_value' => $average_order_value,
        'sales_by_payment_method' => $sales_by_payment_method
    ];
}


private function getSalesTrends($start, $end, $restaurant)
{
    // Sales trends over time
    $sales_trends = Order::selectRaw('DATE(created_at) as date, SUM(amount) as total_sales')
        ->where('restaurant_id', $restaurant->id)
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Best and worst sales days
    $best_sales_day = $sales_trends->max('total_sales');
    $worst_sales_day = $sales_trends->min('total_sales');

    // Peak and off-peak hours
    $sales_by_hour = Order::selectRaw('HOUR(created_at) as hour, SUM(amount) as total_sales')
        ->where('restaurant_id', $restaurant->id)
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

    return [
        'sales_trends' => $sales_trends,
        'best_sales_day' => $best_sales_day,
        'worst_sales_day' => $worst_sales_day,
        'sales_by_hour' => $sales_by_hour
    ];

}

private function getSalesByCategoryItem($start, $end, $restaurant, $category_filter = null)
{
    // Fetch sales by menu category
    $sales_by_category = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->join('dishes', 'order_details.dish_id', '=', 'dishes.id')
        ->where('orders.restaurant_id', $restaurant->id)
        ->whereBetween('orders.created_at', [$start, $end])
        ->when($category_filter, function ($query) use ($category_filter) {
            $query->where('dishes.name', 'like', "%{$category_filter}%");
        })
        ->selectRaw('dishes.name, SUM(order_details.qty) as total_quantity, SUM(order_details.dish_price * order_details.qty) as total_sales')
        ->groupBy('dishes.name')
        ->get();

    return $sales_by_category;
}


private function getDateRange($period)
{
    switch ($period) {
        case 'today':
            $start = Carbon::today();
            $end = Carbon::today();
            break;
        case 'this_week':
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            break;
        case 'this_month':
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            break;
        case 'next_week':
            $start = Carbon::now()->addWeek()->startOfWeek();
            $end = Carbon::now()->addWeek()->endOfWeek();
            break;
        case 'next_month':
            $start = Carbon::now()->addMonth()->startOfMonth();
            $end = Carbon::now()->addMonth()->endOfMonth();
            break;
        case 'previous_week':
            $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
            break;
        case 'previous_month':
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
            break;
        default:
            $start = "";
            $end = "";
    }

    return [
        'start' => $start,
        'end' => $end,
    ];
}




}

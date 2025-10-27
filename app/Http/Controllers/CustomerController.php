<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorUtil;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\ReviewNew;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use ErrorUtil;
    /**
     *
     * @OA\Get(
     *      path="/v1.0/customers",
     *      operationId="getCustomers",
     *      tags={"reports"},
     *       security={
     *           {"bearerAuth": {}}
     *       },

     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=true,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by user creation start date (YYYY-MM-DD)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by user creation end date (YYYY-MM-DD)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="search_key",
     *         in="query",
     *         description="Keyword for searching users by name, email, or phone",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Filter by review rating",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="frequency_visit",
     *         in="query",
     *         description="Visit frequency category (New, Regular, VIP)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="review_start_date",
     *         in="query",
     *         description="Filter reviews by start date (YYYY-MM-DD)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="review_end_date",
     *         in="query",
     *         description="Filter reviews by end date (YYYY-MM-DD)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="review_keyword",
     *         in="query",
     *         description="Keyword to search within review comments",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="frequency_visit",
     *         in="query",
     *         description="Filter users based on visit frequency",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Comma-separated list of booking statuses",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="payment_status",
     *         in="query",
     *         description="Comma-separated list of payment statuses",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="sub_service_ids",
     *         in="query",
     *         description="Comma-separated list of sub-service IDs",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="duration_in_minute",
     *         in="query",
     *         description="Filter by booking duration in minutes",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="booking_type",
     *         in="query",
     *         description="Comma-separated list of booking types",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="date_filter",
     *         in="query",
     *         description="Filter bookings by date (e.g., today, this_week, previous_week, this_month, etc.)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter users by name",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter users by email",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="Filter users by phone number",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="last_visited_date",
     *         in="query",
     *         description="Filter users by last visited date (YYYY-MM-DD)",
     *         required=false,
     *         example=""
     *     ),
     *     @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Sort order for users (ASC or DESC)",
     *         required=false,
     *         example=""
     *     ),
     *      summary="This method is to get  bookings ",
     *      description="This method is to get bookings",
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

    public function getCustomers(Request $request)
    {
        try {
            $this->storeActivity($request, "");

            $restaurant = Restaurant::where([
                "OwnerID" => auth()->user()->id
            ])
                ->first();

          $users = User::withCount([
    'orders as completed_orders_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.status', 'completed');
    },
    'orders as pending_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.status', 'pending');
    },
    'orders as delivery_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.type', 'delivery');
    },
    'orders as eat_in_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.type', 'eat_in');
    },
    'orders as take_away_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.type', 'take_away');
    },
    'orders as website_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_app', 'client');
    },
    'orders as in_store_order_count' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_app', 'pos');
    },
    'orders as first_time_customers' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->whereRaw('(SELECT COUNT(*) FROM orders o WHERE o.customer_id = orders.customer_id AND o.restaurant_id = ?) = 1', [$restaurant->id]);
    },
    'orders as returning_customers' => function ($query) use ($restaurant) {
        $query->where('orders.restaurant_id', $restaurant->id)
            ->whereRaw('(SELECT COUNT(*) FROM orders o WHERE o.customer_id = orders.customer_id AND o.restaurant_id = ?) > 1', [$restaurant->id]);
    }
])

->withSum(['orders as total_revenue_takeaway' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'take_away');
}], 'amount')
->withSum(['orders as total_revenue_delivery' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'delivery');
}], 'amount')
->withSum(['orders as total_revenue_eat_in' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'eat_in');
}], 'amount')
->withAvg(['orders as avg_order_value_takeaway' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'take_away');
}], 'amount')
->withAvg(['orders as avg_order_value_delivery' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'delivery');
}], 'amount')
->withAvg(['orders as avg_order_value_eat_in' => function ($query) use ($restaurant) {
    $query->where('orders.restaurant_id', $restaurant->id)
        ->where('orders.type', 'eat_in');
}], 'amount')






                // Filter by rating if provided
                ->when(request()->filled('rating'), function ($q) {
                    $q->whereHas('reviews', function ($query) {
                        $query->where('review_news.rate', request()->input('rating'));
                    });
                })

                ->when(request()->filled('review_start_date') && request()->filled('review_end_date'), function ($q) {
                    $q->whereHas('reviews', function ($query) {
                        $query->whereBetween('review_news.updated_at', [
                            request()->input('review_start_date'),
                            request()->input('review_end_date')
                        ]);
                    });
                })
                ->when(request()->filled('review_keyword'), function ($q) {
                    $q->whereHas('reviews', function ($query) {
                        $query->where('review_news.comment', 'like', '%' . request('review_keyword') . '%');
                    });
                })
                ->when(request()->filled('frequency_visit'), function ($q) {
                    $frequency = request()->input('frequency_visit');
                    $query_param = '='; // Default value for the comparison operator.
                    $min_count = 1;     // Minimum booking count.
                    $max_count = 1;     // Maximum booking count (for regular customers).

                    if ($frequency == "New") {
                        // For new customers, the count should be exactly 1.
                        $query_param = '=';
                        $min_count = 1;
                        $max_count = 1;
                    } elseif ($frequency == "Regular") {
                        // For regular customers, the count should be between 2 and 5.
                        $query_param = 'BETWEEN';
                        $min_count = 2;
                        $max_count = 5;
                    } elseif ($frequency == "VIP") {
                        // For VIP customers, the count should be 5 or more.
                        $query_param = '>=';
                        $min_count = 5;
                        $max_count = null; // No upper limit for VIP.
                    } else {
                        // Default case or other logic can be applied here.
                        $query_param = '=';
                        $min_count = 1;
                        $max_count = 1;
                    }

                    $q->whereHas("orders", function ($query) use ($query_param, $min_count, $max_count) {
                        // Separate subquery to count all bookings for each customer.
                        $query->whereIn('orders.customer_id', function ($subquery) use ($query_param, $min_count, $max_count) {
                            $subquery->select('orders.customer_id')
                                ->from('orders')
                                ->groupBy('orders.customer_id')
                                ->when($query_param == 'BETWEEN', function ($subquery) use ($min_count, $max_count) {
                                    // If the condition is BETWEEN, use BETWEEN operator.
                                    $subquery->having(DB::raw('COUNT(orders.id)'), 'BETWEEN', [$min_count, $max_count]);
                                })
                                ->when($query_param != 'BETWEEN', function ($subquery) use ($query_param, $min_count) {
                                    // If the condition is '=', '>=', or other operators.
                                    $subquery->having(DB::raw('COUNT(orders.id)'), $query_param, $min_count);
                                });
                        });
                    });
                })


                ->when(!empty($request->name), function ($query) use ($request) {
                    $name = $request->name;
                    return $query->where(function ($subQuery) use ($name) {
                        $subQuery->where("first_Name", "like", "%" . $name . "%")
                            ->orWhere("last_Name", "like", "%" . $name . "%");
                    });
                })
                ->when(!empty($request->email), function ($query) use ($request) {
                    return $query->where('users.email', 'like', '%' . $request->email . '%');
                })
                ->when(!empty($request->phone), function ($query) use ($request) {
                    return $query->where('users.phone', 'like', '%' . $request->phone . '%');
                })
                ->when(!empty($request->last_visited_date), function ($query) use ($request) {
                    return $query->whereHas('lastOrder', function ($query) {
                        return $query->where('orders.created_at', request()->input("last_visited_date"));
                    });
                })

                ->when(!empty($request->search_key), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        $term = $request->search_key;
                        $query;
                    });
                })
                ->when(!empty($request->start_date), function ($query) use ($request) {
                    return $query->where('users.created_at', ">=", $request->start_date);
                })
                ->when(!empty($request->end_date), function ($query) use ($request) {
                    return $query->where('users.created_at', "<=", ($request->end_date . ' 23:59:59'));
                })
                ->when(!empty($request->order_by) && in_array(strtoupper($request->order_by), ['ASC', 'DESC']), function ($query) use ($request) {
                    return $query->orderBy("users.first_Name", $request->order_by);
                }, function ($query) {
                    return $query->orderBy("users.first_Name", "DESC");
                })
                ->when($request->filled("id"), function ($query) use ($request) {
                    return $query
                        ->where("users.id", $request->input("id"))
                        ->first();
                }, function ($query) {
                    return $query->when(!empty(request()->per_page), function ($query) {
                        return $query->paginate(request()->per_page);
                    }, function ($query) {
                        return $query->get();
                    });
                });

            if ($request->filled("id") && empty($users)) {
                throw new Exception("No data found", 404);
            }

          





            return response()->json($users, 200);
        } catch (Exception $e) {

            return $this->sendError($e, 500, $request);
        }
    }


   
}

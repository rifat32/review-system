<?php

namespace App\Http\Controllers;

use App\Http\Utils\DiscountUtil;
use App\Models\Coupon;
use App\Models\Dish;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailsDishes;
use App\Models\OrderDetailsVariation;
use App\Models\OrderVariation;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Models\VariationType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class OrderController extends Controller
{
    use DiscountUtil;
    // ##################################################
    // This method is to store order
    // ##################################################



    public function calculateDishPrice($dish, $order_type)
    {
        $price = $dish->price;
        return $price;

        switch ($order_type) {
            case 'delivery':
                if (!empty($dish->delivery_discounted_price)) {
                    $price = $dish->delivery_discounted_price;
                }
                break;

            case 'eat_in':
                if (!empty($dish->eat_in_discounted_price)) {
                    $price = $dish->eat_in_discounted_price;
                }
                break;

            case 'take_away':
                if (!empty($dish->take_away_discounted_price)) {
                    $price = $dish->take_away_discounted_price;
                }
                break;
        }

        return $price;
    }

    public function canculate_discount($total_price, $discount_type, $discount_amount)
    {
        if (!empty($discount_type) && !empty($discount_amount)) {
            if ($discount_type == "fixed") {
                return $discount_amount;
            } else if ($discount_type == "percentage") {
                return ($total_price / 100) * $discount_amount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }


    public function applyCoupon($order, $coupon)
    {
        if (empty(request()->input("coupon_code"))) {
            return $order; // No coupon to process
        }

        if (request()->input("coupon_code") == $order->coupon_code) {
            return $order; // No coupon to process
        }

        // Increment customer redemptions for the coupon
        Coupon::where([
            "code" => request()->input("coupon_code"),
            "garage_id" => $order->restaurant_id
        ])->update([
            "customer_redemptions" => DB::raw("customer_redemptions + 1")
        ]);

        $discount_type = $coupon->discount_type;
        $discount_amount = $coupon->discount_amount;
        if ($coupon->discount_type == "fixed") {
            $order->coupon_type = $discount_type;
            $order->coupon_amount = $discount_amount;
        } else if ($coupon->discount_type == "percentage") {


            $coupon_dish_ids = $coupon->dishes->pluck("id");

            $booking_dishes = OrderDetail::where([
                "order_id" => $order->id
            ])->get();

            $total_discount = 0;

            foreach ($booking_dishes as $booking_sub_service) {
                if ($coupon_dish_ids->contains($booking_sub_service->dish_id)) {

                    // Apply discount logic here

                    // For example, add a discount to the booking or modify booking_sub_service


                    $discount_amount = $this->canculate_discount(($booking_sub_service->qty * $booking_sub_service->dish_price), "percentage", $coupon->discount_amount);

                    $booking_sub_service->discount_percentage =   $coupon->discount_amount;
                    $booking_sub_service->discounted_price_to_show = ($booking_sub_service->qty * $booking_sub_service->dish_price) - $discount_amount;

                    $booking_sub_service->save();
                    $total_discount += $discount_amount;
                }
            }
            $order->coupon_type = "fixed";
            $order->coupon_amount = $total_discount;
        }


        $order->coupon_code = request()->input("coupon_code");
        $order->save();


        return $order;
    }

    // this function do all the task and returns transaction id or -1
    public function getCouponDiscount($business_id, $code, $amount)
    {

        $coupon =  Coupon::where([
            "business_id" => $business_id,
            "code" => $code,
            "is_active" => 1,

        ])
            // ->where('coupon_start_date', '<=', Carbon::now()->subDay())
            // ->where('coupon_end_date', '>=', Carbon::now()->subDay())
            ->first();

        if (!$coupon) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => ["coupon_code" => "no coupon is found"]
            ];
            throw new Exception(json_encode($error), 422);
        }

        if (!empty($coupon->min_total) && ($coupon->min_total > $amount)) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => ["coupon_code" => "minimim limit is " . $coupon->min_total]
            ];
            throw new Exception(json_encode($error), 422);
        }
        if (!empty($coupon->max_total) && ($coupon->max_total < $amount)) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => "maximum limit is " . $coupon->max_total
            ];
            throw new Exception(json_encode($error), 422);
        }

        if (!empty($coupon->redemptions) && $coupon->redemptions == $coupon->customer_redemptions) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => "maximum people reached"
            ];
            throw new Exception(json_encode($error), 422);
        }

        return $coupon;
    }
    public function createPaymentIntent2(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => (int) round($order->amount * 100), // cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'metadata' => ['order_id' => $order->id],
        ]);

        $order->stripe_payment_intent_id = $paymentIntent->id;
        $order->save();

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }
    public function createPaymentIntent(Request $request, $restaurantId)
    {

        $request_data = [
            'amount' => $request->amount,
            'total_payable_amount' => $request->total_payable_amount,
            'tax' => $request->tax,
            'discount' => $request->discount,
        ];
        // Step 1: Retrieve Stripe settings
        $business = Restaurant::findOrFail($restaurantId);

        if (empty($business) || empty($business->STRIPE_SECRET)) {
            return response()->json(['error' => 'Stripe not configured'], 403);
        }

        $stripe = new \Stripe\StripeClient($business->STRIPE_SECRET);

        // Step 3: Calculate total amount with discounts/tax
        // $discount = $this->canculate_discount_amount($order->price, $order->discount_type, $order->discount_amount);
        // $coupon_discount = $this->canculate_discount_amount($order->price, $order->coupon_discount_type, $order->coupon_discount_amount);
        // $total_discount = $discount + $coupon_discount;

        $totalAmount = ($request_data['total_payable_amount'] + ($request_data['tax'] ?? 0)) - $request_data['discount'];

        // Step 4: Prepare PaymentIntent data
        $paymentIntentData = [
            'amount' => (int) round($totalAmount * 100), // cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'metadata' => [
                'amount' => $request_data['amount'],
                'total_payable_amount' => $request->total_payable_amount,
                'tax' => $request->tax,
                'discount' => $request->discount,
            ],
        ];

        // Step 5: Create PaymentIntent
        $paymentIntent = $stripe->paymentIntents->create($paymentIntentData);


        // Step 7: Return clientSecret to frontend
        return response()->json([
            'paymentIntent' => [
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id
            ],
        ], 201);
    }

    /**
     *
     * @OA\Post(
     *      path="/order/{restaurantId}",
     *      operationId="storeOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store order",
     *      description="This method is to store order",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),

     *  @OA\RequestBody(
     * description="if customer_id null and order type Delivery than a customer will be created",
     *         required=true,
     *         @OA\JsonContent(
     *            required={"amount","customer_id","customer_name","remarks","table_number","type","phone","address","post_code"},
     *      *       @OA\Property(property="request_object", type="string", format="string",example="{}}"),
     *      @OA\Property(property="amount", type="number", format="number",example="50"),
     *      @OA\Property(property="customer_id", type="number", format="number",example="1"),
     *      @OA\Property(property="customer_name", type="string", format="string",example="test"),
     *      @OA\Property(property="remarks", type="string", format="string",example="test"),
     *      @OA\Property(property="table_number", type="string", format="string",example="5"),
     *      @OA\Property(property="type", type="string", format="string",example="test"),
     *      @OA\Property(property="phone", type="string", format="string",example="0111"),
     *      @OA\Property(property="address", type="string", format="string",example="test"),
     * *      @OA\Property(property="post_code", type="string", format="string",example="post_code"),
     *  *      @OA\Property(property="discount", type="string", format="string",example="10"),
     *      *  *      @OA\Property(property="discount_type", type="string", format="string",example="discount_type"),
     *
     * *      @OA\Property(property="cash", type="string", format="string",example="10"),
     *       @OA\Property(property="card", type="string", format="string",example="10"),
     *       @OA\Property(property="customer_note", type="string", format="string",example="10"),
     * *       @OA\Property(property="coupon_code", type="string", format="string",example="10"),


     *       @OA\Property(property="initial_note", type="string", format="string",example="10"),
     *  @OA\Property(property="dishes", type="string", format="array",example={

     *  {		"qty":10,"dish_id":1,
     * "variation_ids":{1,2}
     * },
     *  {		"qty":10,"meal_id":1,
     * "meal_dishes":{
     * {"dish_id":"1","variation_ids":{1,2}
     *
     * }}
     * },
     * }
     *
     * ),
     *
     *
     *
     *
     *

     *         ),
     *      ),
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


    public function storeOrder($restaurantId, Request $request)
    {

        DB::beginTransaction(); // Start the transaction
        try {
            // Check if the restaurant exists based on the provided ID
            $restaurantFound = Restaurant::where([
                "id" => $restaurantId
            ])
                ->first();

            // If the restaurant is not found, return an error response
            if (!$restaurantFound) {
                return response()->json([
                    "message" => "No Business Found with this id"
                ], 404);
            }


            // Prepare the data to be inserted into the orders table
            $insertableOrderData = [
                "order_app" => "pos",
                "amount" => $request->amount,
                "total_due_amount" => $request->total_due_amount,
                "tax" => $request->tax,
                "order_by" => $request->user()->id,
                "table_number" => $request->table_number,
                "customer_name" => $request->customer_name,
                'customer_phone' => $request->phone,
                'customer_post_code' => $request->post_code,
                'customer_address' => $request->address,
                'door_no' => $request->door_no,
                "remarks" => $request->remarks,
                "type" => $request->type,
                "restaurant_id" => $restaurantId,
                "status" => "pending",
                "customer_id" => (!empty($request->customer_id) ? $request->customer_id : NULL),
                'cash' => $request->cash,
                'card' => $request->card,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,

                "request_object" => $request->request_object,
                'customer_note' => $request->customer_note,
                'initial_note' => $request->initial_note,
            ];


            // Create the order record in the database
            $insertedOrder =  Order::create($insertableOrderData);

            // If the order type is delivery, create a new guest user if customer_id is not provided

            if ($request->type == "delivery") {
                if (empty($request->customer_id)) {
                    $insertableUser = [
                        'first_Name' => $request->customer_name,
                        'email' => $request->phone . Str::random(5) . "@gmail.com",
                        'password',
                        'phone' => $request->phone,
                        'type' => "guest user",
                        'post_code' => $request->post_code,
                        "Address" => $request->address,
                        "door_no" => $request->door_no,


                    ];
                    $user =   User::create($insertableUser);
                    $insertedOrder->customer_id = $user->id;
                    $insertedOrder->save();
                }
            }


            // Prepare the notification message for the new order
            $notificationMessage =  "New Order " . $insertedOrder->id . " is placed.";
            if ($request->table_number) {
                $notificationMessage =  "New Order " . $insertedOrder->id . " is placed at Table Number " . $request->table_number;
                RestaurantTable::create([
                    "restaurant_id" => $restaurantId,
                    "status" => "Booked",
                    "table_no" => $request->table_number,
                    "order_id" => $insertedOrder->id,
                ]);
            }

            $notification =  Notification::create([
                'reciever_id' =>  $insertedOrder->customer_id,
                'sender_id'   => $restaurantFound->OwnerID,
                'restaurant_id' => $restaurantFound->id,
                'message' => $notificationMessage,
                'status' => 'unRead'
            ]);

            $order_total_price = 0;

            foreach ($request->dishes as $dish) {

                $dishId = NULL;
                if (!empty($dish["meal_id"])) {
                    $dishId =  $dish["meal_id"];
                }
                if (!empty($dish["dish_id"])) {
                    $dishId =  $dish["dish_id"];
                }

                $dishData = Dish::where([
                    "id" => $dishId
                ])
                    ->first();

                $order_detail_price = $this->calculateDishPrice($dishData, $request->type);

                $order_details =    OrderDetail::create([
                    "custom_id" => !empty($dish["custom_id"]) ? $dish["custom_id"] : NULL,
                    "main_price" =>  $dish["main_price"] ?? 0,
                    "dish_price" =>  $dish["dish_price"] ?? 0,
                    "type" => "take away",
                    "qty" => $dish["qty"],
                    "order_id" => $insertedOrder->id,
                    "dish_id" => !empty($dish["dish_id"]) ? $dish["dish_id"] : NULL,
                    "meal_id" => !empty($dish["meal_id"]) ? $dish["meal_id"] : NULL,
                ]);
                $order_total_price += $order_detail_price * $dish["qty"];


                if (!empty($dish["meal_id"])) {
                    $dealDishes = Dish::leftJoin('deals', 'dishes.id', '=', 'deals.dish_id')
                        ->where([
                            "deals.deal_id" => $dish["meal_id"]
                        ])
                        ->select([
                            "deals.dish_id as dish_id"
                        ])
                        ->get();
                    foreach ($dealDishes as $dealDish) {
                        $found = false;
                        foreach ($dish["meal_dishes"] as $meal_dish) {
                            if ($dealDish->dish_id == $meal_dish["dish_id"]) {
                                $found = true;
                                $order_details_dish = OrderDetailsDishes::create([
                                    "order_details_id" => $order_details->id,
                                    "dish_id" =>  $meal_dish["dish_id"],
                                ]);
                                foreach ($meal_dish["variation_ids"] as $variation) {
                                    OrderDetailsVariation::create([
                                        "order_details_dish_id" => $order_details_dish->id,
                                        "variation_id" => $variation,
                                    ]);
                                }
                            }
                        }
                        if (!$found) {
                            $order_details_dish = OrderDetailsDishes::create([
                                "order_details_id" => $order_details->id,
                                "dish_id" =>  $dealDish->dish_id,
                            ]);
                        }
                    }
                } else {
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderDetailsVariation::create([
                            "dish_id" => $dishId,
                            "order_details_id" => $order_details->id,
                            "variation_id" => $variation,
                        ]);
                    }
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderVariation::create([
                            "dish_id" => $dishId,
                            "order_id" => $insertedOrder->id,
                            "variation_id" => $variation,
                        ]);
                    }
                }
            }


            if (!empty($request_data["coupon_code"])) {
                $coupon = $this->getCouponDiscount(
                    $restaurantId,
                    $request->coupon_code,
                    $request->amount
                );
                $insertedOrder = $this->applyCoupon($insertedOrder, $coupon);
            }

            // $insertedOrder->amount = $order_total_price;

            $insertedOrder->final_price = $order_total_price;

            // $insertedOrder->final_price -= $this->canculate_discount_amount(
            //     $insertedOrder->amount,
            //     $insertedOrder->coupon_discount_type,
            //     $insertedOrder->coupon_discount_amount
            // );

            $discount_amount = $this->canculate_discount_amount(
                $insertedOrder->amount,
                $insertedOrder->discount_type,
                $insertedOrder->discount
            );

            $insertedOrder->final_price -= $discount_amount;

            if ($request->final_price != $insertedOrder->final_price) {
                $loggedDetails = [
                    'expected_final_price' => $insertedOrder->final_price,
                    'received_amount' => $request->amount,
                    'order_total_price' => $order_total_price,
                    'discount_applied' =>  $discount_amount,
                    'products' => $request->dishes,
                ];

                // throw new Exception(
                //     "Mismatch in price: expected final price is " . $insertedOrder->final_price .
                //     ", but received amount is " . $request->amount .
                //     ". Details: " . json_encode($loggedDetails),
                //     409
                // );
            }



            if ($insertedOrder->amount <= ($insertedOrder->cash + $insertedOrder->card)) {
                $insertedOrder->payment_status = 'paid';
            } else {
                $insertedOrder->payment_status = 'unpaid';
            }


            $insertedOrder->save();

            DB::commit();
            return response()->json([
                "message" => "order inserted",
                "data" => $insertedOrder
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $errorData = [
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ];

            return response()->json($errorData, 500);
        }
    }



    // ##################################################
    // This method is to store order by user
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/order/orderbyuser/{restaurantId}",
     *      operationId="storeByUser",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store order by user",
     *      description="This method is to store order by user",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),

     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"amount","customer_id","customer_name","remarks","table_number","type","phone","address","post_code"},
     *             @OA\Property(property="request_object", type="string", format="string",example="{}}"),
     *     @OA\Property(property="amount", type="number", format="number",example="50"),
     *     @OA\Property(property="total_due_amount", type="number", format="number",example="50"),
     *     @OA\Property(property="tax", type="number", format="number",example="50"),
     *
     *      @OA\Property(property="customer_id", type="number", format="number",example="1"),
     *      @OA\Property(property="customer_name", type="string", format="string",example="test"),
     *      @OA\Property(property="remarks", type="string", format="string",example="test"),
     *      @OA\Property(property="table_number", type="string", format="string",example="5"),
     *      @OA\Property(property="type", type="string", format="string",example="test"),
     *      @OA\Property(property="phone", type="string", format="string",example="0111"),
     *      @OA\Property(property="address", type="string", format="string",example="test"),
     * *      @OA\Property(property="post_code", type="string", format="string",example="post_code"),
     *  *      @OA\Property(property="discount", type="string", format="string",example="10"),
     *   *  *      @OA\Property(property="discount_type", type="string", format="string",example="10"),
     *
     *
     * *      @OA\Property(property="cash", type="string", format="string",example="10"),
     *       @OA\Property(property="card", type="string", format="string",example="10"),
     *  @OA\Property(property="dishes", type="string", format="array",example={

     *  {		"qty":10,"dish_id":1,
     * "variation_ids":{1,2}
     * },
     *  {		"qty":10,"meal_id":1,
     * "meal_dishes":{
     * {"dish_id":"1","variation_ids":{1,2}
     *
     * }}
     * },
     * }
     *
     * ),
     *
     *
     *
     *
     *

     *         ),
     *      ),
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


    public function storeByUser($restaurantId, Request $request)
    {

        DB::beginTransaction(); // Start the transaction
        try {



            $restaurantFound = Restaurant::where([
                "id" => $restaurantId
            ])
                ->first();
            if (!$restaurantFound) {
                return response()->json([
                    "message" => "No Business Found with this id"
                ], 404);
            }


            $names = explode(' ', $request->customer_name);
            $first_name = $names[0];
            $last_name = implode(' ', array_slice($names, 1));

            $insertableOrderData = [
                "order_app" => "client",
                "amount" => $request->amount,
                "total_due_amount" => $request->amount,
                "tax" => $request->tax,


                "order_by" => $request->user()->id,
                "customer_id" => $request->user()->id,
                "table_number" => $request->table_number,
                'first_Name' => $first_name,
                'last_Name' => $last_name,
                'customer_phone' => $request->phone,
                'customer_post_code' => $request->post_code,
                'customer_address' => $request->address,
                'door_no' => $request->door_no,
                "remarks" => $request->remarks,
                "type" => $request->type,
                "restaurant_id" => $restaurantId,
                "status" => "pending",
                'cash' => $request->cash,
                'card' => $request->card,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,

                "request_object" => $request->request_object,

                "order_time" => $request->order_time,

            ];

            $insertedOrder =  Order::create($insertableOrderData);



            if ($request->type == "delivery") {

                if (!$request->user()->id) {
                    $names = explode(' ', $request->customer_name);
                    $first_name = $names[0];
                    $last_name = implode(' ', array_slice($names, 1));
                    $insertableUser = [
                        'first_Name' => $first_name,
                        'last_Name' => $last_name,
                        'email' => $request->phone . Str::random(5) . "@gmail.com",
                        'password',
                        'phone' => $request->phone,
                        'type' => "guest user",
                        'post_code' => $request->post_code,
                        "Address" => $request->address,
                        "door_no" => $request->door_no,


                    ];
                    $user =   User::create($insertableUser);
                    $insertedOrder->customer_id = $user->id;
                    $insertedOrder->save();
                }
            }


            $notificationMessage =  "New Order " . $insertedOrder->id . " is placed.";
            if ($request->table_number) {
                $notificationMessage =  "New Order " . $insertedOrder->id . " is placed at Table Number " . $request->table_number;
                RestaurantTable::create([
                    "restaurant_id" => $restaurantId,
                    "status" => "Booked",
                    "table_no" => $request->table_number,
                    "order_id" => $insertedOrder->id,
                ]);
            }

            $notification =  Notification::create([
                'reciever_id' =>  $insertedOrder->customer_id,
                'sender_id'   => $restaurantFound->OwnerID,
                'restaurant_id' => $restaurantFound->id,
                'message' => $notificationMessage,
                'status' => 'unRead'
            ]);

            $order_total_price = 0;
            foreach ($request->dishes as $dish) {
                error_log(json_encode($dish["qty"]));
                $dishId = NULL;
                if (!empty($dish["meal_id"])) {
                    $dishId =  $dish["meal_id"];
                }
                if (!empty($dish["dish_id"])) {
                    $dishId =  $dish["dish_id"];
                }

                $dishData = Dish::where([
                    "id" => $dishId
                ])
                    ->first();

                $order_detail_price = $this->calculateDishPrice($dishData, $request->type);
                $order_details =    OrderDetail::create([

                    "type" => "take away",
                    "main_price" =>  $dish["main_price"] ?? 0,
                    "dish_price" =>  $dish["dish_price"] ?? 0,
                    "qty" => $dish["qty"],
                    "order_id" => $insertedOrder->id,
                    "dish_id" => !empty($dish["dish_id"]) ? $dish["dish_id"] : NULL,
                    "meal_id" => !empty($dish["meal_id"]) ? $dish["meal_id"] : NULL,
                ]);

                $order_total_price += $order_detail_price * $dish["qty"];





                if (!empty($dish["meal_id"])) {
                    $dealDishes = Dish::leftJoin('deals', 'dishes.id', '=', 'deals.dish_id')
                        ->where([
                            "deals.deal_id" => $dish["meal_id"]
                        ])
                        ->select([
                            "deals.dish_id as dish_id"
                        ])
                        ->get();
                    foreach ($dealDishes as $dealDish) {
                        $found = false;
                        foreach ($dish["meal_dishes"] as $meal_dish) {
                            if ($dealDish->dish_id == $meal_dish["dish_id"]) {
                                $found = true;
                                $order_details_dish = OrderDetailsDishes::create([
                                    "order_details_id" => $order_details->id,
                                    "dish_id" =>  $meal_dish["dish_id"],
                                ]);
                                foreach ($meal_dish["variation_ids"] as $variation) {
                                    OrderDetailsVariation::create([
                                        "order_details_dish_id" => $order_details_dish->id,
                                        "variation_id" => $variation,
                                    ]);
                                }
                            }
                        }
                        if (!$found) {
                            $order_details_dish = OrderDetailsDishes::create([
                                "order_details_id" => $order_details->id,
                                "dish_id" =>  $dealDish->dish_id,
                            ]);
                        }
                    }
                } else {
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderDetailsVariation::create([
                            "dish_id" => $dishId,
                            "order_details_id" => $order_details->id,
                            "variation_id" => $variation,
                        ]);
                    }
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderVariation::create([
                            "dish_id" => $dishId,
                            "order_id" => $insertedOrder->id,
                            "variation_id" => $variation,
                        ]);
                    }
                }
            }


            if (!empty($request_data["coupon_code"])) {
                $coupon = $this->getCouponDiscount(
                    $restaurantId,
                    $request->coupon_code,
                    $request->amount
                );
                $insertedOrder = $this->applyCoupon($insertedOrder, $coupon);
            }

            // $insertedOrder->amount = $order_total_price;
            $insertedOrder->final_price = $order_total_price;

            $discount_amount = $this->canculate_discount_amount(
                $insertedOrder->amount,
                $insertedOrder->coupon_discount_type,
                $insertedOrder->coupon_discount_amount
            );

            $insertedOrder->final_price -= $discount_amount;


            if ($request->final_price != $insertedOrder->final_price) {
                $loggedDetails = [
                    'expected_final_price' => $insertedOrder->final_price,
                    'received_amount' => $request->amount,
                    'order_total_price' => $order_total_price,
                    'discount_applied' =>  $discount_amount,
                    'products' => $request->dishes,
                ];

                // throw new Exception(
                //     "Mismatch in price: expected final price is " . $insertedOrder->final_price .
                //     ", but received amount is " . $request->amount .
                //     ". Details: " . json_encode($loggedDetails),
                //     409
                // );
            }

            if ($insertedOrder->amount <= ($insertedOrder->cash + $insertedOrder->card)) {
                $insertedOrder->payment_status = 'paid';
            } else {
                $insertedOrder->payment_status = 'unpaid';
            }


            $insertedOrder->save();

            DB::commit();
            return response()->json([
                "message" => "order inserted",
                "order" => $insertedOrder
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $errorData = [
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ];
            return response()->json($errorData, 500);
        }
    }

    // ##################################################
    // This method is to complete order
    // ##################################################

    /**
     *
     * @OA\Patch(
     *      path="/order/{orderId}",
     *      operationId="orderComplete",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to complete order",
     *      description="This method is to complete order",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),

     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"card","cash"},
     *      @OA\Property(property="card", type="number", format="number",example="50"),
     *      @OA\Property(property="cash", type="number", format="string",example="200"),

     *

     *         ),
     *      ),
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


    public function     orderComplete($orderId, Request $request)
    {



        $updatedOrder =    tap(Order::where(["id" => $orderId]))->update(
            [
                "status" => "completed",
                "card" => $request->card,
                "cash" => $request->cash,
            ]
        )
            // ->with("somthing")

            ->first();
        RestaurantTable::where([
            "order_id" => $orderId
        ])
            ->delete();
        return response($updatedOrder, 200);
    }
    // ##################################################
    // This method is to update order status
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/order/updatestatus/{orderId}",
     *      operationId="updateStatus",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update order status",
     *      description="This method is to update order status",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),

     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"status"},
     *      @OA\Property(property="status", type="string", format="string",example="active"),


     *         ),
     *      ),
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


    public function     updateStatus($orderId, Request $request)
    {

        $updatedOrder =    tap(Order::where(["id" => $orderId]))->update(
            [
                "status" => $request->status,
            ]
        )
            // ->with("somthing")

            ->first();

        return response($updatedOrder, 200);
    }
    // ##################################################
    // This method is to edit order
    // ##################################################



    /**
     *
     * @OA\Patch(
     *      path="/order/edit/order/{orderId}",
     *      operationId="editOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to edit order",
     *      description="This method is to edit order",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),

     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"amount","customer_id","customer_name","remarks","table_number","type","phone","address","post_code"},
     *      @OA\Property(property="amount", type="number", format="number",example="50"),

     *      @OA\Property(property="customer_name", type="string", format="string",example="test"),
     *      @OA\Property(property="remarks", type="string", format="string",example="test"),
     *      @OA\Property(property="table_number", type="string", format="string",example="5"),
     *      @OA\Property(property="type", type="string", format="string",example="test"),
     *      @OA\Property(property="phone", type="string", format="string",example="0111"),
     *      @OA\Property(property="address", type="string", format="string",example="test"),
     * *      @OA\Property(property="post_code", type="string", format="string",example="post_code"),
     *  *      @OA\Property(property="discount", type="string", format="string",example="10"),
     *  *      @OA\Property(property="discount_type", type="string", format="string",example="discount_type"),
     *
     * *      @OA\Property(property="cash", type="string", format="string",example="10"),
     *       @OA\Property(property="card", type="string", format="string",example="10"),
     *       @OA\Property(property="customer_note", type="string", format="string",example="10"),
     *       @OA\Property(property="initial_note", type="string", format="string",example="10"),




     *       @OA\Property(property="request_object", type="string", format="string",example="{}}"),



     *
     *  @OA\Property(property="dishes", type="string", format="array",example={

     *  {		"qty":10,"dish_id":1,
     * "variation_ids":{1,2}
     * },
     *  {		"qty":10,"meal_id":1,
     * "meal_dishes":{
     * {"dish_id":"1","variation_ids":{1,2}
     *
     * }}
     * },
     * }
     *
     * ),
     *

     *

     *         ),
     *      ),
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



    public function     editOrder($orderId, Request $request)
    {

        DB::beginTransaction(); // Start the transaction
        try {
            RestaurantTable::where([
                "order_id" => $orderId
            ])
                ->delete();
            OrderVariation::where([
                "order_id" => $orderId
            ])
                ->delete();
            $order_details =  OrderDetail::where([
                "order_id" => $orderId
            ])
                ->get();
            OrderDetail::where([
                "order_id" => $orderId
            ])
                ->delete();


            $updatableData = [
                "amount" => $request->amount,
                "total_due_amount" => $request->total_due_amount,
                "tax" => $request->tax,
                "order_by" => $request->user()->id,
                "table_number" => $request->table_number,
                "customer_name" => $request->customer_name,
                'customer_phone' => $request->phone,
                'customer_post_code' => $request->post_code,
                'customer_address' => $request->address,
                'door_no' => $request->door_no,

                "remarks" => $request->remarks,
                "type" => $request->type,
                "status" => "pending",
                'cash' => $request->cash,
                'card' => $request->card,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,

                "request_object" => $request->request_object,

                "customer_note" => $request->customer_note,
                "initial_note" => $request->initial_note
            ];
            $updatedOrder =    tap(Order::where(["id" => $orderId]))->update(
                $updatableData
            )
                ->first();
            if (!$updatedOrder) {
                return response()->json([
                    "message" => "invalid order"
                ], 404);
            }





            if ($request->table_number) {

                RestaurantTable::create([
                    "restaurant_id" => $updatedOrder->restaurant_id,
                    "status" => "Booked",
                    "table_no" => $request->table_number,
                    "order_id" => $updatedOrder->id,
                ]);
            }



            $order_total_price = 0;
            foreach ($request->dishes as $dish) {
                error_log(json_encode($dish["qty"]));
                $dishId = NULL;
                if (!empty($dish["meal_id"])) {
                    $dishId =  $dish["meal_id"];
                }
                if (!empty($dish["dish_id"])) {
                    $dishId =  $dish["dish_id"];
                }

                $dishData = Dish::where([
                    "id" => $dishId
                ])
                    ->first();

                $order_detail_price = $this->calculateDishPrice($dishData, $request->type);
                $order_details =    OrderDetail::create([

                    "type" => "take away",
                    "main_price" =>  $dish["main_price"] ?? 0,
                    "dish_price" =>  $dish["dish_price"] ?? 0,
                    "qty" => $dish["qty"],
                    "order_id" => $updatedOrder->id,
                    "dish_id" => !empty($dish["dish_id"]) ? $dish["dish_id"] : NULL,
                    "meal_id" => !empty($dish["meal_id"]) ? $dish["meal_id"] : NULL,
                ]);
                $order_total_price += $order_detail_price * $dish["qty"];





                if (!empty($dish["meal_id"])) {
                    $dealDishes = Dish::leftJoin('deals', 'dishes.id', '=', 'deals.dish_id')
                        ->where([
                            "deals.deal_id" => $dish["meal_id"]
                        ])
                        ->select([
                            "deals.dish_id as dish_id"
                        ])
                        ->get();
                    foreach ($dealDishes as $dealDish) {
                        $found = false;
                        foreach ($dish["meal_dishes"] as $meal_dish) {
                            if ($dealDish->dish_id == $meal_dish["dish_id"]) {
                                $found = true;
                                $order_details_dish = OrderDetailsDishes::create([
                                    "order_details_id" => $order_details->id,
                                    "dish_id" =>  $meal_dish["dish_id"],
                                ]);
                                foreach ($meal_dish["variation_ids"] as $variation) {
                                    OrderDetailsVariation::create([
                                        "order_details_dish_id" => $order_details_dish->id,
                                        "variation_id" => $variation,
                                    ]);
                                }
                            }
                        }
                        if (!$found) {
                            $order_details_dish = OrderDetailsDishes::create([
                                "order_details_id" => $order_details->id,
                                "dish_id" =>  $dealDish->dish_id,
                            ]);
                        }
                    }
                } else {
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderDetailsVariation::create([
                            "dish_id" => $dishId,
                            "order_details_id" => $order_details->id,
                            "variation_id" => $variation,
                        ]);
                    }
                    foreach ($dish["variation_ids"] as $variation) {
                        OrderVariation::create([
                            "dish_id" => $dishId,
                            "order_id" => $updatedOrder->id,
                            "variation_id" => $variation,
                        ]);
                    }
                }
            }

            if (!empty($request_data["coupon_code"])) {
                $coupon = $this->getCouponDiscount(
                    $updatedOrder->restaurant_id,
                    $request->coupon_code,
                    $request->amount
                );
                $updatedOrder = $this->applyCoupon($updatedOrder, $coupon);
            }

            // $updatedOrder->amount = $order_total_price;



            $updatedOrder->final_price = $order_total_price;

            // Step 1: Apply coupon discount first
            // Step 1: Apply coupon discount first
            $coupon_discount = $this->canculate_discount_amount(
                $updatedOrder->amount,
                $updatedOrder->coupon_discount_type,
                $updatedOrder->coupon_discount_amount
            );

            // Step 2: Apply additional discount on the reduced amount
            $final_discount = $coupon_discount + $this->canculate_discount_amount(
                $updatedOrder->amount - $coupon_discount, // Apply on the already reduced amount
                $updatedOrder->discount_type,
                $updatedOrder->discount
            );


            $updatedOrder->final_price -= $final_discount;



            if ($request->final_price != $updatedOrder->final_price) {
                $loggedDetails = [
                    'expected_final_price' => $updatedOrder->final_price,
                    'received_amount' => $request->amount,
                    'order_total_price' => $order_total_price,
                    'discount_applied' =>  $final_discount,
                    'products' => $request->dishes,
                ];


                // throw new Exception(
                //     "Mismatch in price: expected final price is " . $updatedOrder->final_price .
                //     ", but received amount is " . $request->amount .
                //     ". Details: " . json_encode($loggedDetails),
                //     409
                // );

            }

            if ($updatedOrder->amount <= ($updatedOrder->cash + $updatedOrder->card)) {
                $updatedOrder->payment_status = 'paid';
            } else {
                $updatedOrder->payment_status = 'unpaid';
            }

            $updatedOrder->save();

            DB::commit();
            return response()->json([
                "message" => "order updated"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $errorData = [
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ];

            return response()->json($errorData, $e->getCode());
        }
    }
    // ##################################################
    // This method is to delete order
    // ##################################################

    /**
     *
     * @OA\Delete(
     *      path="/order/{orderId}",
     *      operationId="deleteOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to delete order",
     *      description="This method is to delete order",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),

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


    public function     deleteOrder($orderId)
    {
        RestaurantTable::where([
            "order_id" => $orderId
        ])
            ->delete();
        OrderVariation::where([
            "order_id" => $orderId
        ])
            ->delete();
        OrderDetail::where([
            "order_id" => $orderId
        ])
            ->delete();

        Order::where(["id" => $orderId])
            ->delete();





        return response()->json([
            "message" => "order deleted"
        ]);
    }
    // ##################################################
    // This method is to get order by id
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/order/{orderId}",
     *      operationId="getOrderById",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order by id",
     *      description="This method is to get order by id",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),

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


    public function     getOrderById($orderId)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->where(["id" => $orderId])
            ->first();

        return response()->json($orders);
    }

    /**
     *
     * @OA\Get(
     *      path="/order/by-restaurant/{orderId}/{restaurantId}",
     *      operationId="getOrderById2",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order by id",
     *      description="This method is to get order by id",
     *  @OA\Parameter(
     * name="orderId",
     * in="path",
     * description="orderId",
     * required=true,
     * example="1"
     * ),
     *     @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),

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


    public function     getOrderById2($orderId, $restaurantId)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )->where([
            "id" => $orderId,
            "restaurant_id" => $restaurantId

        ])
            ->first();

        return response()->json($orders);
    }

    // ##################################################
    // This method is to get order by customer id
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/order/getorderby/customerid/{customerId}",
     *      operationId="getOrderByCustomerId",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order by customer id",
     *      description="This method is to get order by customer id",
     *  @OA\Parameter(
     * name="customerId",
     * in="path",
     * description="customerId",
     * required=true,
     * example="1"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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



    public function     getOrderByCustomerId($customerId)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })

            ->where(["customer_id" => $customerId])
            ->get();

        return response()->json($orders);
    }
    // ##################################################
    // This method is to get todays order by status
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/order/orderlist/today/{status}",
     *      operationId="getTodaysOrderByStatus",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get todays order by status",
     *      description="This method is to get todays order by status",
     *  @OA\Parameter(
     * name="status",
     * in="path",
     * description="status",
     * required=true,
     * example="1"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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





    public function     getTodaysOrderByStatus($status)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([

                "status" => $status
            ])
            ->where("created_at", ">=", Carbon::today())
            ->get();

        return response()->json($orders);
    }


    // ##################################################
    // This method is to get all order
    // ##################################################
    /**
     *
     * @OA\Get(
     *      path="/order/All/order",
     *      operationId="getAllOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),
     *      summary="This method is to get all order",
     *      description="This method is to get all order",

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




    public function     getAllOrder()
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->latest()
            ->get();

        return response()->json($orders);
    }
    /**
     *
     * @OA\Get(
     *      path="/order/All/order/today/{restaurantId}",
     *      operationId="getAllOrderToday",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get todays order",
     *      description="This method is to get todays order ",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *      *  @OA\Parameter(
     * name="order_type",
     * in="path",
     * description="order_type",
     * required=false,
     * example=""
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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

    public function     getAllOrderToday($restaurantId)
    {
        $orderTypes = ["delivery", "eat_in", "take_away"];

        if (request()->has("order_type")) {
            $orderType = request()->input("order_type");
            if (in_array($orderType, $orderTypes)) {
                $orderTypes = [$orderType];
            }
        }

        $data = [];

        foreach ($orderTypes as $orderType) {
            $data["total_" . $orderType . "_orders"] = Order::with(
                'restaurant',

                "detail.dish",

                "detail.meal",
                "detail.meal_variations.dish.deal.dish",
                "detail.meal_variations.dish_variation.variation.variation_type",
                "detail.variations.variation.variation_type",


                "ordervariation.variation",
                "user"
            )->where([
                "restaurant_id" => $restaurantId
            ])
                ->when(request()->has("order_app"), function ($query) {
                    $query->where("orders.order_app", request()->input("order_app"));
                })
                ->where("created_at", ">=", Carbon::today())
                ->where([
                    "type" => $orderType
                ])
                ->get()
                ->map(function ($order) {
                    $order->detail = $order->detail->map(function ($orderDetail) {
                        // Pluck variation_ids from the orderDetail's variations
                        $variation_ids = $orderDetail->variations->pluck("variation_id");

                        // Fetch and assign variation_types based on variation_ids
                        $orderDetail->variation_types = VariationType::with([
                            'variation' => function ($query) use ($variation_ids) {
                                $query->whereIn("variations.id", $variation_ids);
                            }
                        ])
                            ->whereHas("variation", function ($query) use ($variation_ids) {
                                $query->whereIn("variations.id", $variation_ids);
                            })
                            ->get(); // Now you have the variation_types for each orderDetail

                        return $orderDetail;
                    });

                    return $order;
                })->values();
        }

        return response()->json($data);
    }


    /**
     *
     * @OA\Get(
     *      path="/order/All/order/every/{perPage}/{restaurantId}",
     *      operationId="getAllOrderEveryDay",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get everyday order",
     *      description="This method is to get everyday order ",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *  @OA\Parameter(
     * name="perPage",
     * in="path",
     * description="perPage",
     * required=true,
     * example="1"
     * ),

     *  @OA\Parameter(
     * name="from_date",
     * in="query",
     * description="from_date",
     * required=true,
     * example="2019-06-29"
     * ),

     *  @OA\Parameter(
     * name="to_date",
     * in="query",
     * description="to_date",
     * required=true,
     * example="2019-06-29"
     * ),


     *  @OA\Parameter(
     * name="min_amount",
     * in="query",
     * description="min_amount",
     * required=true,
     * example="10"
     * ),

     *  @OA\Parameter(
     * name="max_amount",
     * in="query",
     * description="max_amount",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="table_number",
     * in="query",
     * description="table_number",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_name",
     * in="query",
     * description="customer_name",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_phone",
     * in="query",
     * description="customer_phone",
     * required=true,
     * example="100"
     * ),


     *  @OA\Parameter(
     *      name="type[]",
     *      in="query",
     *      description="type",
     *      required=true,
     *      example="type1,type2,type3"
     * ),




     *  @OA\Parameter(
     *      name="status[]",
     *      in="query",
     *      description="status",
     *      required=true,
     *      example="status1,status2,status3"
     * ),
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),





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

    public function     getAllOrderEveryDay($perPage, $restaurantId, Request $request)
    {

        $query = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "restaurant_id" => $restaurantId
            ]);


        if (!empty($request->from_date)) {
            $query = $query->where("orders.created_at", ">=", $request->from_date);
        }
        if (!empty($request->to_date)) {
            $query = $query->where("orders.created_at", "<=", $request->to_date);
        }

        //   $types = !empty($request->type) ? $request->type : array();
        //   if (is_array($types)) {
        //       $types = implode(',', $types);
        //   }
        //   $types = explode(',', $types);



        //   if(count($types)){

        //     $query = $query->whereIn("orders.type",$types);
        // }

        if (!empty($request->min_amount)) {
            $query = $query->where("orders.amount", "<=", $request->min_amount);
        }
        if (!empty($request->max_amount)) {
            $query = $query->where("orders.amount", ">=", $request->max_amount);
        }
        if (!empty($request->table_number)) {
            $query = $query->where("orders.table_number", $request->table_number);
        }


        //   $statuses = !empty($request->status) ? $request->status : array();
        //   if (is_array($statuses)) {
        //       $statuses = implode(',', $statuses);
        //   }
        //   $statuses = explode(',', $statuses);

        //   if(count($statuses)){

        //     $query = $query->whereIn("orders.status",$statuses);
        // }
        if (!empty($request->type)) {
            $null_filter = collect(array_filter($request->type))->values();
            $type =  $null_filter->all();
            if (count($type)) {
                $query =   $query->whereIn("orders.type", $type);
            }
        }
        if (!empty($request->status)) {
            $null_filter = collect(array_filter($request->status))->values();
            $status =  $null_filter->all();
            if (count($status)) {
                $query =   $query->whereIn("orders.status", $status);
            }
        }


        if (!empty($request->customer_name)) {
            $query = $query->where("orders.customer_name", $request->customer_name);
        }

        if (!empty($request->customer_phone)) {
            $query = $query->where("orders.customer_name", $request->customer_phone);
        }



        $data  =   $query
            ->orderByDesc("orders.id")

            ->paginate($perPage);



        return response()->json($data);
    }

    /**
     *
     * @OA\Get(
     *      path="/order/All/order/every/{restaurantId}",
     *      operationId="getAllOrderEveryDayV2",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get everyday order",
     *      description="This method is to get everyday order ",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),


     *  @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="per_page",
     * required=true,
     * example="2"
     * ),

     *  @OA\Parameter(
     * name="from_date",
     * in="query",
     * description="from_date",
     * required=true,
     * example="2019-06-29"
     * ),

     *  @OA\Parameter(
     * name="to_date",
     * in="query",
     * description="to_date",
     * required=true,
     * example="2019-06-29"
     * ),


     *  @OA\Parameter(
     * name="min_amount",
     * in="query",
     * description="min_amount",
     * required=true,
     * example="10"
     * ),

     *  @OA\Parameter(
     * name="max_amount",
     * in="query",
     * description="max_amount",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="table_number",
     * in="query",
     * description="table_number",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_name",
     * in="query",
     * description="customer_name",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_phone",
     * in="query",
     * description="customer_phone",
     * required=true,
     * example="100"
     * ),


     *  @OA\Parameter(
     *      name="type[]",
     *      in="query",
     *      description="type",
     *      required=true,
     *      example="type1,type2,type3"
     * ),




     *  @OA\Parameter(
     *      name="status[]",
     *      in="query",
     *      description="status",
     *      required=true,
     *      example="status1,status2,status3"
     * ),
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),





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

    public function     getAllOrderEveryDayV2($restaurantId, Request $request)
    {
        $query = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "restaurant_id" => $restaurantId
            ]);


        if (!empty($request->from_date)) {
            $query = $query->where("orders.created_at", ">=", $request->from_date);
        }
        if (!empty($request->to_date)) {
            $query = $query->where("orders.created_at", "<=", $request->to_date);
        }

        //   $types = !empty($request->type) ? $request->type : array();
        //   if (is_array($types)) {
        //       $types = implode(',', $types);
        //   }
        //   $types = explode(',', $types);



        //   if(count($types)){

        //     $query = $query->whereIn("orders.type",$types);
        // }

        if (!empty($request->min_amount)) {
            $query = $query->where("orders.amount", "<=", $request->min_amount);
        }
        if (!empty($request->max_amount)) {
            $query = $query->where("orders.amount", ">=", $request->max_amount);
        }
        if (!empty($request->table_number)) {
            $query = $query->where("orders.table_number", $request->table_number);
        }


        //   $statuses = !empty($request->status) ? $request->status : array();
        //   if (is_array($statuses)) {
        //       $statuses = implode(',', $statuses);
        //   }
        //   $statuses = explode(',', $statuses);

        //   if(count($statuses)){

        //     $query = $query->whereIn("orders.status",$statuses);
        // }
        if (!empty($request->type)) {
            $null_filter = collect(array_filter($request->type))->values();
            $type =  $null_filter->all();
            if (count($type)) {
                $query =   $query->whereIn("orders.type", $type);
            }
        }
        if (!empty($request->status)) {
            $null_filter = collect(array_filter($request->status))->values();
            $status =  $null_filter->all();
            if (count($status)) {
                $query =   $query->whereIn("orders.status", $status);
            }
        }


        if (!empty($request->customer_name)) {
            $query = $query->where("orders.customer_name", $request->customer_name);
        }

        if (!empty($request->customer_phone)) {
            $query = $query->where("orders.customer_name", $request->customer_phone);
        }



        $orders  =   $query
            ->orderByDesc("orders.id")
            ->paginate(!empty($request->per_page) ? $request->per_page : 10);


        $data["orders"] = $orders;
        $data["date_range"] = [$request->from_date, $request->to_date];

        $orderTypes = ["delivery", "eat_in", "take_away"];

        foreach ($orderTypes as $orderType) {

            $data["report_data"][$orderType . "_orders"] = collect($orders->items())->filter(function ($item) use ($orderType) {
                return $item->type === $orderType; // Assuming there's a 'type' field in the order data
            });

            $data["report_data"][$orderType . "_orders_count"] = $data["report_data"][$orderType . "_orders"]->count();
            $data["report_data"][$orderType . "_orders_total_income"] = $data["report_data"][$orderType . "_orders"]->sum("amount");
        }





        return response()->json($data);
    }
    /**
     *
     * @OA\Get(
     *      path="/v3.0/order/All/order/every/{restaurantId}",
     *      operationId="getAllOrderEveryDayV3",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get everyday order",
     *      description="This method is to get everyday order ",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),


     *  @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="per_page",
     * required=true,
     * example="2"
     * ),

     *  @OA\Parameter(
     * name="from_date",
     * in="query",
     * description="from_date",
     * required=true,
     * example="2019-06-29"
     * ),

     *  @OA\Parameter(
     * name="to_date",
     * in="query",
     * description="to_date",
     * required=true,
     * example="2019-06-29"
     * ),


     *  @OA\Parameter(
     * name="min_amount",
     * in="query",
     * description="min_amount",
     * required=true,
     * example="10"
     * ),

     *  @OA\Parameter(
     * name="max_amount",
     * in="query",
     * description="max_amount",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="table_number",
     * in="query",
     * description="table_number",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_name",
     * in="query",
     * description="customer_name",
     * required=true,
     * example="100"
     * ),

     *  @OA\Parameter(
     * name="customer_phone",
     * in="query",
     * description="customer_phone",
     * required=true,
     * example="100"
     * ),


     *  @OA\Parameter(
     *      name="type[]",
     *      in="query",
     *      description="type",
     *      required=true,
     *      example="type1,type2,type3"
     * ),




     *  @OA\Parameter(
     *      name="status[]",
     *      in="query",
     *      description="status",
     *      required=true,
     *      example="status1,status2,status3"
     * ),
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),





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

    public function getAllOrderEveryDayV3($restaurantId, Request $request)
    {
        $orderTypes = ["delivery", "eat_in", "take_away"];

        // Build validation rules dynamically for page parameters
        $errors = [];
        foreach ($orderTypes as $orderType) {
            $pageParam = "page_{$orderType}";
            if (!$request->has($pageParam) || !is_numeric($request->input($pageParam)) || $request->input($pageParam) < 1) {
                $errors[$pageParam] = "The {$pageParam} field is required and must be an integer greater than or equal to 1.";
            }
        }

        // Return errors if validation fails
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $data["date_range"] = [$request->input('from_date'), $request->input('to_date')];

        foreach ($orderTypes as $orderType) {
            $query = Order::with(
                'restaurant',
                "detail.dish",
                "detail.meal",
                "detail.meal_variations.dish.deal.dish",
                "detail.meal_variations.dish_variation.variation.variation_type",
                "detail.variations.variation.variation_type",
                "ordervariation.variation",
                "user"
            )
                ->when($request->has("order_app"), function ($query) use ($request) {
                    $query->where("orders.order_app", $request->input("order_app"));
                })
                ->where([
                    "restaurant_id" => $restaurantId
                ])
                ->where("orders.type", $orderType);

            if (!empty($request->from_date)) {
                $query = $query->where("orders.created_at", ">=", $request->from_date);
            }
            if (!empty($request->to_date)) {
                $query = $query->where("orders.created_at", "<=", $request->to_date);
            }
            if (!empty($request->min_amount)) {
                $query = $query->where("orders.amount", "<=", $request->min_amount);
            }
            if (!empty($request->max_amount)) {
                $query = $query->where("orders.amount", ">=", $request->max_amount);
            }
            if (!empty($request->table_number)) {
                $query = $query->where("orders.table_number", $request->table_number);
            }
            if (!empty($request->status)) {
                $null_filter = collect(array_filter($request->status))->values();
                $status = $null_filter->all();
                if (count($status)) {
                    $query = $query->whereIn("orders.status", $status);
                }
            }
            if (!empty($request->customer_name)) {
                $query = $query->where("orders.customer_name", $request->customer_name);
            }
            if (!empty($request->customer_phone)) {
                $query = $query->where("orders.customer_name", $request->customer_phone);
            }

            // Use validated page parameter for each order type
            $pageParam = $request->input("page_{$orderType}");

            $orders = $query
                ->orderByDesc("orders.id")
                ->paginate(
                    !empty($request->per_page) ? $request->per_page : 10,
                    ['*'], // Columns
                    "page", // Default page query parameter
                    $pageParam // Override page number
                );

            // After pagination, map the details to add extra data
            $orders->getCollection()->transform(function ($order) {
                $order->detail = $order->detail->map(function ($orderDetail) {
                    // Pluck variation_ids from the orderDetail's variations
                    $variation_ids = $orderDetail->variations->pluck("variation_id");

                    // Fetch and assign variation_types based on variation_ids
                    $orderDetail->variation_types = VariationType::with([
                        'variation' => function ($query) use ($variation_ids) {
                            $query->whereIn("variations.id", $variation_ids);
                        }
                    ])
                        ->whereHas("variation", function ($query) use ($variation_ids) {
                            $query->whereIn("variations.id", $variation_ids);
                        })
                        ->get(); // Now you have the variation_types for each orderDetail

                    return $orderDetail;
                });

                return $order;
            });

            // The result is now paginated with extra data
            $orders->values();

            $data["orders"][$orderType] = $orders;
        }

        return response()->json($data);
    }

    // Helper function to validate dates
    private function isValidDate($date)
    {
        return (bool)strtotime($date);
    }





    /**
     *
     * @OA\Get(
     *      path="/order/All/order-by-type/{type}",
     *      operationId="getOrderByType",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order by type",
     *      description="This method is to get order by type",
     *  @OA\Parameter(
     * name="type",
     * in="path",
     * description="type",
     * required=true,
     * example="eat-----in"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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





    public function     getOrderByType($type, Request $request)
    {

        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "type" => $type
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     *
     * @OA\Get(
     *      path="/order/All/order-by-type/by-restaurant/{type}/{restaurantId}",
     *      operationId="getOrderByType2",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order by type and restaurant id",
     *      description="This method is to get order by type restaurant id",
     *  @OA\Parameter(
     * name="type",
     * in="path",
     * description="type",
     * required=true,
     * example="eat-----in"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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





    public function     getOrderByType2($type, $restaurantId, Request $request)
    {

        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "type" => $type,
                "restaurant_id" => $restaurantId
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // ##################################################
    // This method is to get all pending order
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/order/All/pending/order/{restaurantId}",
     *      operationId="getAllPendingOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get all pending order",
     *      description="This method is to get all pending order",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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



    public function     getAllPendingOrder($restaurantId)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "restaurant_id" => $restaurantId,
                "status" => "pending"
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }
    /**
     *
     * @OA\Get(
     *      path="/order/All/pending/order/{restaurantId}/{perPage}",
     *      operationId="getAllPendingOrderWithPagination",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get all pending order with pagination",
     *      description="This method is to get all pending order with pagination",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *  @OA\Parameter(
     * name="perPage",
     * in="path",
     * description="perPage",
     * required=true,
     * example="1"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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



    public function     getAllPendingOrderWithPagination($restaurantId, $perPage)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "restaurant_id" => $restaurantId,
                "status" => "pending"
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json($orders);
    }
    // ##################################################
    // This method is to get all autoprint order
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/order/All/autoprint/order/{restaurantId}",
     *      operationId="getAllAutoPrintOrder",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get all autoprint order",
     *      description="This method is to get all autoprint order",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),

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





    public function     getAllAutoPrintOrder($restaurantId)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where([
                "restaurant_id" => $restaurantId,
                "autoprint" => true
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }
    // ##################################################
    // This method is to get daily order report
    // ##################################################



    /**
     *
     * @OA\Get(
     *      path="/order/get/daily/order/report",
     *      operationId="getdailyOrderReport",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),
     *      summary="This method is to get daily order report",
     *      description="This method is to get daily order report",
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




    public function     getdailyOrderReport()
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where("created_at", ">=", Carbon::today())
            ->get();

        $data["Delivery"] = 0;
        $data["DineIn"] = 0;
        $data["Takeaway"] = 0;
        foreach ($orders as $order) {
            if ($order->type == "delivery") {
                $data["Delivery"] += 1;
            }
            if ($order->type == "DineIn") {
                $data["DineIn"] += 1;
            }
            if ($order->type == "Takeaway") {
                $data["Takeaway"] += 1;
            }
        }

        return response()->json($data);
    }




    // ##################################################
    // This method is to get order report
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/order/oderreporting/{min}/{max}/{fromdate}/{todate}/{status}",
     *      operationId="getOrderReport",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order report",
     *      description="This method is to get order report",
     *  @OA\Parameter(
     * name="min",
     * in="path",
     * description="min",
     * required=true,
     * example="0"
     * ),
     *  @OA\Parameter(
     * name="max",
     * in="path",
     * description="max",
     * required=true,
     * example="99"
     * ),
     *  @OA\Parameter(
     * name="fromdate",
     * in="path",
     * description="fromdate",
     * required=true,
     * example="2019-06-29"
     * ),
     *  @OA\Parameter(
     * name="todate",
     * in="path",
     * description="todate",
     * required=true,
     * example="2023-06-29"
     * ),

     *  @OA\Parameter(
     * name="status",
     * in="path",
     * description="status",
     * required=true,
     * example="pending"
     * ),
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),


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



    public function     getOrderReport($min, $max, $fromdate, $todate, $status)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->whereBetween("amount", [$min, $max])
            ->whereBetween("created_at", [$fromdate, $todate])
            ->where("status", $status)
            ->latest()
            ->get();

        return response()->json($orders);
    }
    // ##################################################
    // This method is to get order report by restaurant id
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/order/oderreporting/{restaurantId}/{min}/{max}/{fromdate}/{todate}/{status}",
     *      operationId="getorderReportByRestaurantId",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order report by restaurant id",
     *      description="This method is to get order report by restaurant id",
     *       @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="0"
     * ),
     *  @OA\Parameter(
     * name="min",
     * in="path",
     * description="min",
     * required=true,
     * example="0"
     * ),
     *  @OA\Parameter(
     * name="max",
     * in="path",
     * description="max",
     * required=true,
     * example="99"
     * ),
     *  @OA\Parameter(
     * name="fromdate",
     * in="path",
     * description="fromdate",
     * required=true,
     * example="2019-06-29"
     * ),
     *  @OA\Parameter(
     * name="todate",
     * in="path",
     * description="todate",
     * required=true,
     * example="2023-06-29"
     * ),

     *  @OA\Parameter(
     * name="status",
     * in="path",
     * description="status",
     * required=true,
     * example="pending"
     * ),
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),


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




    public function     getorderReportByRestaurantId($restaurantId, $min, $max, $fromdate, $todate, $status)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->whereBetween("amount", [$min, $max])
            ->whereBetween("created_at", [$fromdate, $todate])
            ->where("status", $status)
            ->where("restaurant_id", $restaurantId)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     *
     * @OA\Get(
     *      path="/order/oderreporting/{restaurantId}/{fromdate}/{todate}/{status}",
     *      operationId="getorderReportByRestaurantId2",
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get order report by restaurant id",
     *      description="This method is to get order report by restaurant id",
     *       @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="0"
     * ),
     *  @OA\Parameter(
     * name="min",
     * in="path",
     * description="min",
     * required=true,
     * example="0"
     * ),
     *  @OA\Parameter(
     * name="max",
     * in="path",
     * description="max",
     * required=true,
     * example="99"
     * ),
     *    *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),
     *  @OA\Parameter(
     * name="fromdate",
     * in="path",
     * description="fromdate",
     * required=true,
     * example="2019-06-29"
     * ),
     *  @OA\Parameter(
     * name="todate",
     * in="path",
     * description="todate",
     * required=true,
     * example="2023-06-29"
     * ),

     *  @OA\Parameter(
     * name="status",
     * in="path",
     * description="status",
     * required=true,
     * example="pending"
     * ),



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




    public function     getorderReportByRestaurantId2($restaurantId, $fromdate, $todate, $status)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->whereBetween("created_at", [$fromdate, $todate])
            ->where("status", $status)
            ->where("restaurant_id", $restaurantId)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // ##################################################
    // This method is to get order by user
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/order/byuser/all/order",
     *      operationId="getOrderByUser",
     *
     *      tags={"order"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *  *    *   *              @OA\Parameter(
     *         name="order_app",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
     *      ),
     *      summary="This method is to get order by user",
     *      description="This method is to get order by user",
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






    public function     getOrderByUser(Request $request)
    {
        $orders = Order::with(
            'restaurant',
            "detail.dish",
            "detail.meal",
            "detail.meal_variations.dish.deal.dish",
            "detail.meal_variations.dish_variation.variation.variation_type",

            "detail.variations.variation.variation_type",
            "ordervariation.variation",
            "user"
        )
            ->when(request()->has("order_app"), function ($query) {
                $query->where("orders.order_app", request()->input("order_app"));
            })
            ->where(["customer_id" => $request->user()->id])
            ->get();

        return response()->json($orders);
    }


    /**
     *
     * @OA\Get(
     *      path="/order/get/table-information/{restaurantId}",
     *      operationId="getTableInformation",
     *      tags={"report"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get table report",
     *      description="This method is to get table report",
     *       @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="0"
     * ),

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



    public function getTableInformation(Request $request, $restaurantId)
    {

        // $res_tables = RestaurantTable::where([
        //     "restaurant_tables.restaurant_id" => $restaurantId
        // ])
        // ->leftJoin("orders","orders.id", "=","restaurant_tables.order_id")
        // ->where([
        //     "orders.status" => "pending"
        // ])
        //     ->get();
        $res_tables = Order::where([
            "orders.restaurant_id" => $restaurantId,
            "orders.status" => "pending",
            "orders.type" => "eat_in"
        ])

            ->whereDate('created_at', Carbon::today())

            ->get();
        $restaurant = Restaurant::where([
            "id" => $restaurantId
        ])
            ->first();
        $data["busy_table"] = [];
        $data["available_table"] = [];
        $data["total_tables"] = $restaurant->totalTables;
        for ($i = 1; $i <= $restaurant->totalTables; $i++) {
            $free = true;
            foreach ($res_tables as $res_table) {
                if ($res_table->table_number == $i) {
                    array_push($data["busy_table"], $i);
                    $free = false;
                }
            }
            if ($free) {
                array_push($data["available_table"], $i);
            }
        }

        return response()->json($data, 200);
        // $restaurantId
    }
}

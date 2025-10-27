<?php

namespace App\Http\Controllers;

use App\Http\Utils\DiscountUtil;
use App\Models\Deal;
use App\Models\Dish;
use App\Models\Restaurant;
use Exception;
use Illuminate\Http\Request;

class DishController extends Controller
{
    use DiscountUtil;
    // ##################################################
    // This method is to store dish
    // ##################################################

    /**
     *
     * @OA\Post(
     *      path="/dishes/{menuId}",
     *      operationId="storeDish",
     *      tags={"dishes"},

     *      summary="This method is to store dish",
     *      description="This method is to store dish",
     *  @OA\Parameter(
     * name="menuId",
     * in="path",
     * description="method",
     * required=true,
     * example="1"
     * ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","price","restaurant_id","take_away","delivery","description","ingredients","calories"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test@g.c"),
     *            @OA\Property(property="price", type="string", format="string",example="12345678"),
     *  @OA\Property(property="take_away_discounted_price", type="string", format="string",example="12345678"),
     *      *  @OA\Property(property="eat_in_discounted_price", type="string", format="string",example="12345678"),
     *    @OA\Property(property="delivery_discounted_price", type="string", format="string",example="12345678"),
     *            @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *             @OA\Property(property="take_away", type="string", format="string",example="1"),
     *             @OA\Property(property="delivery", type="string", format="string",example="0"),
     *            @OA\Property(property="description", type="string", format="string",example="description"),
     *              @OA\Property(property="ingredients", type="string", format="string",example="ingredients"),
     *              @OA\Property(property="calories", type="string", format="string",example="calories"),
     *             @OA\Property(property="order_number", type="number", format="number",example="1"),
     *              @OA\Property(property="preparation_time", type="number", format="number",example="1"),
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





    public function storeDish($menuId, Request $request)
    {
        $dish_data = $request->toArray();

        $dish_data["menu_id"] = $menuId;

        $dish =  Dish::create($dish_data);

        // If time slots exist in request, save them
        if (!empty($dish_data['is_time_based'])) {
            if (empty($dish_data['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            foreach ($dish_data['time_slots'] as $slot) {
                $dish->time_slots()->create([
                "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }


        return response($dish, 200);
    }


    // ##################################################
    // This method is to update dish
    // ##################################################




    public function updateDish($dishId, Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $imageName = time() . '.' . $request->image->extension();



        $request->image->move(public_path('img/dish'), $imageName);

        $imageName = "img/dish/" . $imageName;



        $dish = tap(Dish::where(["id" => $dishId]))->update(
            [
                "name" => $request->name,
                "price" => $request->price,

                "take_away_discounted_price" => $request->take_away_discounted_price,
                "eat_in_discounted_price" => $request->eat_in_discounted_price,
                "delivery_discounted_price" => $request->delivery_discounted_price,




                "order_number" => $request->order_number,
                "preparation_time" => $request->preparation_time,

                "restaurant_id" => $request->restaurant_id,
                "image" => $imageName,

            ]
        )
            // ->with("somthing")

            ->first();

        if (!empty($request['is_time_based'])) {
            if (empty($request['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            $dish->time_slots()->delete();
            foreach ($request['time_slots'] as $slot) {
                $dish->time_slots()->create([
                "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }



        return response($dish, 200);
    }
    // ##################################################
    // This method is to update dish image
    // ##################################################


    /**
     *
     * @OA\Post(
     *      path="/dishes/uploadimage/{dishId}",
     *      operationId="updateDishImage",
     *      tags={"dishes"},

     *      summary="This method is to update dish image",
     *      description="This method is to update dish image",
     *  @OA\Parameter(
     * name="dishId",
     * in="path",
     * description="dishId",
     * required=true,
     * example="1"
     * ),
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="image",
     *                     type="file",
     *                ),
     *                 required={"image"}
     *             )
     *         )
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



    public function updateDishImage($dishId, Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $imageName = time() . '.' . $request->image->extension();



        $request->image->move(public_path('img/dish'), $imageName);

        $imageName = "img/dish/" . $imageName;



        $dish =    tap(Dish::where(["id" => $dishId]))->update(
            [

                "image" => $imageName,

            ]
        )
            // ->with("somthing")

            ->first();


        return response($dish, 200);
    }
    public function getRestaurantById($restaurant_id)
    {
        $restaurant = Restaurant::where(["id" => $restaurant_id])->first();
        return $restaurant;
    }
    // ##################################################
    // This method is to get all dish
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/dishes/All/dishes/{restaurantId}",
     *      operationId="getAllDishes",
     *      tags={"dishes"},

     *      summary="This method is to get all dish",
     *      description="This method is to get all dish",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
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




    public function getAllDishes($restaurantId, Request $request)
    {

        $restaurant = $this->getRestaurantById($restaurantId);
        $dishes = Dish::with(
            "menu",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "deal.dish.menu",
            "deal.dish.dish_variations",
            "deal.dish.dish_variations.variation_type",
            "deal.dish.dish_variations.variation_type.variation",
            "time_slots"
        )

            ->where(function ($query) use ($restaurantId) {
                $query->where([
                    "restaurant_id" => $restaurantId
                ])
                    ->orWhereHas("menu", function ($query) use ($restaurantId) {
                        $query->where([
                            "menus.restaurant_id" => $restaurantId
                        ]);
                    })
                ;
            })
            ->orderBy("dishes.order_number")
            ->filter($restaurant)

            ->get();

        foreach ($dishes as $dish) {
            $dish = $this->calculateCampaigns($dish);
        }

        return response($dishes, 200);
    }

    /**
     *
     * @OA\Get(
     *      path="/v2/dishes/All/dishes/{restaurantId}",
     *      operationId="getAllDishesV2",
     *      tags={"dishes"},

     *      summary="This method is to get all dish",
     *      description="This method is to get all dish",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
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



    public function getAllDishesV2($restaurantId, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);

        $dishVariations = Dish::with(
            "menu",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "deal.dish.menu",
            "deal.dish.dish_variations",
            "deal.dish.dish_variations.variation_type",
            "deal.dish.dish_variations.variation_type.variation",
            "time_slots"
        )
            ->filter($restaurant)
            ->where(function ($query) use ($restaurantId) {
                $query->where([
                    "restaurant_id" => $restaurantId
                ])
                    ->orWhereHas("menu", function ($query) use ($restaurantId) {
                        $query->where([
                            "menus.restaurant_id" => $restaurantId
                        ]);
                    })
                ;
            })

            ->orderBy("dishes.order_number")

            ->get();

        foreach ($dishVariations as $dish) {
            $dish = $this->calculateCampaigns($dish);
        }
        return response($dishVariations, 200);
    }




    /**
     *
     * @OA\Get(
     *      path="/dishes/All/dishes/{restaurantId}/{perPage}",
     *      operationId="getAllDishesWithPagination",
     *      tags={"dishes"},

     *      summary="This method is to get all dish with pagination",
     *      description="This method is to get all dish with pagination",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
     *    @OA\Parameter(
     *         name="perPage",
     *         in="path",
     *         description="perPage",
     *         required=true,
     * example="1"
     *      ),
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



    public function getAllDishesWithPagination($restaurantId, $perPage, Request $request)
    {

        $restaurant = $this->getRestaurantById($restaurantId);
        $dishVariations = Dish::with(
            "menu",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "time_slots"


        )->where([
            "restaurant_id" => $restaurantId
        ])
            ->filter($restaurant)
            ->orderBy("dishes.order_number")
            ->paginate($perPage);


        return response($dishVariations, 200);
    }



    // ##################################################
    // This method is to get dish by menu id
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/dishes/{menuId}",
     *      operationId="getDisuBuMenuId",
     *      tags={"dishes"},

     *      summary=" This method is to get dish by menu id",
     *      description="This method is to get dish by menu id",
     *
     *  *            @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
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



    public function getDisuBuMenuId($menuId, Request $request)
    {

        $restaurant = Restaurant::whereHas("menus", function ($query) use ($menuId) {
            $query->where("menus.id", $menuId);
        })
            ->first();
        // with variation, dis_variation
        $dishes = Dish::with(
            "menu",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "time_slots"

        )->where([
            "menu_id" => $menuId
        ])
            ->filter($restaurant)
            ->orderBy("dishes.order_number")
            ->get();

        foreach ($dishes as $dish) {
            $dish = $this->calculateCampaigns($dish);
        }
        return response($dishes, 200);
    }
    /**
     *
     * @OA\Get(
     *      path="/dishes/by-restaurant/{menuId}/{restaurantId}",
     *      operationId="getDisuBuMenuId2",
     *      tags={"dishes"},

     *      summary=" This method is to get dish by menu id",
     *      description="This method is to get dish by menu id",
     *
     *  *            @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *  *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
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



    public function getDisuBuMenuId2($menuId, $restaurantId, Request $request)
    {

        $restaurant = $this->getRestaurantById($restaurantId);
        // with variation, dis_variation
        $dishVariations = Dish::with("menu", "dish_variations", "dish_variations.variation_type", "dish_variations.variation_type.variation", "deal.dish", "time_slots")->where([
            "menu_id" => $menuId,
            "restaurant_id" => $restaurantId,
            "type" => NULL
        ])
            ->filter($restaurant)
            ->orderBy("dishes.order_number")
            ->get();

        return response($dishVariations, 200);
    }
    /**
     *
     * @OA\Get(
     *      path="/deal-dishes/by-restaurant/{menuId}/{restaurantId}",
     *      operationId="getDealDisuBuMenuId2",
     *      tags={"dishes"},

     *      summary=" This method is to get dish by menu id",
     *      description="This method is to get dish by menu id",
     *
     *  *            @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *  *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
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



    public function getDealDisuBuMenuId2($menuId, $restaurantId, Request $request)
    {

        $restaurant = $this->getRestaurantById($restaurantId);
        // with variation, dis_variation
        $dishVariations = Dish::with("menu", "dish_variations", "dish_variations.variation_type", "dish_variations.variation_type.variation", "deal.dish", "time_slots")->where([
            "menu_id" => $menuId,
            "restaurant_id" => $restaurantId,
            "type" => "deal"
        ])
            ->filter($restaurant)
            ->orderBy("dishes.order_number")
            ->get();

        return response($dishVariations, 200);
    }


    // ##################################################
    // This method is to get dish by deal id
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/dishes/getdealsdishes/{dealId}",
     *      operationId="getDishByDealId",
     *      tags={"dishes"},

     *      summary="This method is to get dish by deal id",
     *      description="This method is to get dish by deal id",
     *
     *  *            @OA\Parameter(
     *         name="dealId",
     *         in="path",
     *         description="dealId",
     *         required=true,
     * example="1"
     *      ),
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




    public function getDishByDealId($dealId, Request $request)
    {

        $restaurant = Restaurant::whereHas("dishes", function ($query) use ($dealId) {
            $query->whereHas("deal", function ($query) use ($dealId) {
                $query->where("deals.deal_id", $dealId);
            });
        })
            ->first();

        $dishVariations = Dish::with("menu", "dish_variations", "dish_variations.variation_type", "dish_variations.variation_type.variation", "deal.dish", "time_slots")

            ->leftJoin('deals', 'dishes.id', '=', 'deals.dish_id')
            ->where([
                "deals.deal_id" => $dealId
            ])
            ->orderBy("dishes.order_number")
            ->filter($restaurant)

            ->get();

        return response($dishVariations, 200);
    }
    /**
     *
     * @OA\Get(
     *      path="/dishes/getdealsdishes/{dealId}/{restaurantId}",
     *      operationId="getDishByDealId2",
     *      tags={"dishes"},

     *      summary="This method is to get dish by deal id",
     *      description="This method is to get dish by deal id",
     *
     *  *            @OA\Parameter(
     *         name="dealId",
     *         in="path",
     *         description="dealId",
     *         required=true,
     * example="1"
     *      ),
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




    public function getDishByDealId2($dealId, $restaurantId, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $dishVariations = Dish::with("menu", "dish_variations", "dish_variations.variation_type", "dish_variations.variation_type.variation", "deal.dish", "time_slots")

            ->leftJoin('deals', 'dishes.id', '=', 'deals.dish_id')
            ->where([
                "deals.dish_id" => $dealId,
                "dishes.restaurant_id" => $restaurantId
            ])
            ->filter($restaurant)
            ->orderBy("dishes.order_number")
            ->get();

        return response($dishVariations, 200);
    }


    // ##################################################
    // This method is to get dish by deal id
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/dishes/by-dishid/{dishId}",
     *      operationId="getDishById",
     *      tags={"dishes"},

     *      summary="This method is to get dish by  id",
     *      description="This method is to get dish by  id",
     *
     *  *            @OA\Parameter(
     *         name="dishId",
     *         in="path",
     *         description="dishId",
     *         required=true,
     * example="1"
     *      ),
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




    public function getDishById($dishId, Request $request)
    {

        $restaurant = Restaurant::whereHas("dishes", function ($query) use ($dishId) {
            $query->where("id", $dishId);
        })
            ->first();
        $dish = Dish::with(
            "menu.time_slots",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "deal.dish.dish_variations",
            "deal.dish.dish_variations.variation_type",
            "deal.dish.dish_variations.variation_type.variation",
            "time_slots"

        )
            ->where([
                "id" => $dishId
            ])
            ->filter($restaurant)

            ->first();


        $dish = $this->calculateCampaigns($dish);

        return response($dish, 200);
    }
    /**
     *
     * @OA\Get(
     *      path="/dishes/by-dishid/{dishId}/{restaurantId}",
     *      operationId="getDishById2",
     *      tags={"dishes"},

     *      summary="This method is to get dish by  id",
     *      description="This method is to get dish by  id",
     *
     *  *            @OA\Parameter(
     *         name="dishId",
     *         in="path",
     *         description="dishId",
     *         required=true,
     * example="1"
     *      ),
     *  *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
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




    public function getDishById2($dishId, $restaurantId, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $dish = Dish::with("deal.dish", "menu", "time_slots")
            ->where([
                "id" => $dishId,
                "restaurant_id" => $restaurantId
            ])
            ->filter($restaurant)

            ->first();

        return response($dish, 200);
    }




    // ##################################################
    // This method is to get all dish with deals
    // ##################################################



    /**
     *
     * @OA\Get(
     *      path="/dishes/getusermenu/dealsdishes",
     *      operationId="getAllDishesWithDeals",
     *      tags={"dishes"},

     *      summary="This method is to get all dish with deals",
     *      description="This method is to get all dish with deals",
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







    public function getAllDishesWithDeals(Request $request)
    {
        // with variation, dis_variation
        $dishVariations = Dish::with("deal.dish", "menu", "dish_variations", "dish_variations.variation_type", "dish_variations.variation_type.variation", "dish_variations.dish", "time_slots")

            ->orderBy("dishes.order_number")
            ->get();

        return response($dishVariations, 200);
    }
    // ##################################################
    // This method is to store multiple  dish
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/dishes/multiple/{restaurantId}",
     *      operationId="storeMultipleDish",
     *      tags={"dishes"},

     *      summary="This method is to store multiple  dish",
     *      description="This method is to store multiple  dish",
     *
     *              @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"dishes"},
     *  @OA\Property(property="dishes", type="string", format="array",example={
     *  {	"name":"hggggg","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555,"take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth", "order_number":1, "preparation_time":1},
     *  {	"name":"hggggg","price":555,"take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth", "order_number":1, "preparation_time":1},
     *

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

    public function storeMultipleDish($restaurantId, Request $request)
    {
        $dishes = $request->dishes;
        $dishes_array = [];
        foreach ($dishes as $dish) {
            $dish["restaurant_id"] = $restaurantId;

            $createdDish =  Dish::create($dish);
            if (!empty($dish['is_time_based'])) {
                if (empty($dish['time_slots'])) {
                    return response(["message" => "time_slots are required"], 400);
                }
                foreach ($dish['time_slots'] as $slot) {

                    $createdDish->time_slots()->create([
                "is_active" => $slot['is_active'],
                        'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                    ]);
                }
            }
            array_push($dishes_array, $createdDish);
        }

        return response($dishes_array, 201);
    }





    // ##################################################
    // This method is to store single deal dish
    // ##################################################


    /**
     *
     * @OA\Post(
     *      path="/dishes/single/deal/{menuId}",
     *      operationId="storeDealDish",
     *      tags={"z.unused"},

     *      summary="This method is to store single deal dish",
     *      description="This method is to store single deal dish",
     *
     *              @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","price","restaurant_id","take_away","delivery","description","ingredients","calories","selected"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test@g.c"),
     *            @OA\Property(property="price", type="string", format="string",example="50"),
     *
     *      *            @OA\Property(property="take_away_discounted_price", type="string", format="string",example="50"),
     *      *            @OA\Property(property="eat_in_discounted_price", type="string", format="string",example="50"),
     *      *            @OA\Property(property="delivery_discounted_price", type="string", format="string",example="50"),
     *            @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *             @OA\Property(property="take_away", type="string", format="string",example="1"),
     *             @OA\Property(property="delivery", type="string", format="string",example="0"),
     *            @OA\Property(property="description", type="string", format="string",example="description"),
     *              @OA\Property(property="ingredients", type="string", format="string",example="ingredients"),
     *              @OA\Property(property="calories", type="string", format="string",example="calories"),
     *              @OA\Property(property="order_number", type="number", format="number",example="1"),
     *              @OA\Property(property="preparation_time", type="number", format="number",example="1"),
     *
     *  @OA\Property(property="selected", type="string", format="array",example={

     *  {		"dish_id":"1"
     * },
     *  {		"dish_id":"2"
     * }
     *

     * }
     *
     * ),
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


    public function storeDealDish($menuId, Request $request)
    {
        $dish_data = $request->toArray();

        $dish_data["menu_id"] = $menuId;
        $dish_data["type"] = "deal";
        // @@@@ this image link should be changed
        //  $dish_data["image"] =    "https://images.unsplash.com/photo-1594315590298-329f49c8dcb9?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8dGhlJTIwc3VufGVufDB8fDB8fA%3D%3D&w=1000&q=80";

        $data['dish'] =  Dish::create($dish_data);

        // If time slots exist in request, save them
        if (!empty($dish_data['is_time_based'])) {
            if (empty($dish_data['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            foreach ($dish_data['time_slots'] as $slot) {
                $data['dish']->time_slots()->create([
                "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }


        $data["deals"] = [];

        foreach ($body["selected"] as $selected) {
            $requestDeal = [
                "deal_id" => $data['dish']->id,
                "dish_id" => $selected["dish_id"]
            ];
            $createdDeal = Deal::create($requestDeal);
            array_push($data["deals"], $createdDeal);
        }


        return response($data, 201);
    }



    // ##################################################
    // This method is to store multiple deal dish
    // ##################################################


    /**
     *
     * @OA\Post(
     *      path="/dishes/multiple/deal/{menuId}",
     *      operationId="storeMultipleDealDish",
     *      tags={"dishes"},

     *      summary="This method is to store multiple deal dish",
     *      description="This method is to store multiple deal dish",
     *
     *              @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"dishes"},


     *  @OA\Property(property="dishes", type="string", format="array",example={

     *  {		"name":"hggggg","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555,    "take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","selected":{{"dish_id":"1"},"order_number":1, "preparation_time":1}
     * },
     *  {		"name":"hggggg","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555,"take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","selected":{{"dish_id":"1"},"order_number":1, "preparation_time":1}
     * }
     *

     * }
     *
     * ),
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


    public function storeMultipleDealDish($menuId, Request $request)
    {
        $dishes = $request->dishes;
        $data["dishes"] = [];
        $data["deals"] = [];
        foreach ($dishes as $dish_data) {
            $dish_data["menu_id"] = $menuId;

            $dish_data["type"] = "deal";
            // @@@@ this image link should be changed
            // $dish["image"] =    "https://images.unsplash.com/photo-1594315590298-329f49c8dcb9?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8dGhlJTIwc3VufGVufDB8fDB8fA%3D%3D&w=1000&q=80";

            $createdDish =  Dish::create($dish_data);

            if (!empty($dish_data['is_time_based'])) {
                if (empty($dish_data['time_slots'])) {
                    return response(["message" => "time_slots are required"], 400);
                }
                foreach ($dish_data['time_slots'] as $slot) {
                    $createdDish->time_slots()->create([
                "is_active" => $slot['is_active'],
                        'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                    ]);
                }
            }

            array_push($data["dishes"], $createdDish);
            foreach ($dish_data["selected"] as $selected) {
                $requestDeal = [
                    "deal_id" => $createdDish->id,
                    "dish_id" => $selected["dish_id"]
                ];
                $createdDeal = Deal::create($requestDeal);
                array_push($data["deals"], $createdDeal);
            }
        }

        return response($data, 201);
    }


    /**
     *
     * @OA\Patch(
     *      path="/dishes/multiple/deal/{menuId}",
     *      operationId="updateMultipleDealDish",
     *      tags={"dishes"},

     *      summary="This method is to update multiple deal dish",
     *      description="This method is to update multiple deal dish",
     *
     *                @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"dishes"},
     *  @OA\Property(property="dishes", type="string", format="array",example={
     *  {"id":1,"name":"hggggg","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555, "order_number":1,"preparation_time":"1", "take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","selected":{{"dish_id":"1"}}
     * },
     *  { "id":2,	"name":"hggggg","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555, "order_number":1, "preparation_time":1, "take_away":1,"delivery": 0,"restaurant_id": 56,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","selected":{{"dish_id":"1"}}
     * }
     *

     * }
     *
     * ),
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


    public function updateMultipleDealDish($menuId, Request $request)
    {

        $dishes = $request->dishes;
        $data["dishes"] = [];
        $data["deals"] = [];
        foreach ($dishes as $dish_data) {

            $dish_data["menu_id"] = $menuId;
            $updated_dish = Dish::where([
                "id" => $dish_data["id"]
            ])
                ->first();

            $updated_dish->update(
                collect($dish_data)->only([
                    'name',
                    'price',
                    "take_away_discounted_price",
                    "eat_in_discounted_price",
                    "delivery_discounted_price",
                    "order_number",
                    "preparation_time",
                    "take_away",
                    "delivery",
                    "description",
                    "ingredients",
                    "calories",
                    "menu_id"
                ])->all()
            );

            if (!empty($dish_data['is_time_based'])) {
                if (empty($dish_data['time_slots'])) {
                    return response(["message" => "time_slots are required"], 400);
                }
                $updated_dish->time_slots()->delete();
                foreach ($dish_data['time_slots'] as $slot) {
                    $updated_dish->time_slots()->create([
                "is_active" => $slot['is_active'],
                        'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                    ]);
                }
            }


            Deal::where([
                "deal_id" => $dish_data["id"]
            ])
                ->delete();




            foreach ($dish_data["selected"] as $selected) {
                $requestDeal = [
                    "deal_id" => $dish_data["id"],
                    "dish_id" => $selected["dish_id"]
                ];
                $createdDeal = Deal::create($requestDeal);
                array_push($data["deals"], $createdDeal);

                error_log(json_encode($createdDeal));
            }
        }

        return response(["message" => "data updated"], 201);
    }
    // ##################################################
    // This method is to update multiple dish
    // ##################################################



    /**
     *
     * @OA\Patch(
     *      path="/dishes/Edit/multiple",
     *      operationId="updateMultipleDish",
     *      tags={"dishes"},

     *      summary="This method is to update multiple dish",
     *      description="This method is to update multiple dish",
     *
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"dishes"},


     *  @OA\Property(property="dishes", type="string", format="array",example={

     *  {		"name":"aaaaa","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555,"order_number":1,"preparation_time":1, "take_away":1,"delivery": 0,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","id":2},
     *  {		"name":"aaaaa","price":555,"take_away_discounted_price":555,"eat_in_discounted_price":555,"delivery_discounted_price":555, "order_number":1,"preparation_time":1, "take_away":1,"delivery": 0,"description":"fffffffffff","ingredients":"hgggxrth srthdhh thgg","calories":"cfgt trfgh s rth","id":2},
     *

     * }
     *
     * ),
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

    public function updateMultipleDish(Request $request)
    {
        $dishes = $request->dishes;
        $dishes_array = [];

        foreach ($dishes as $dish_data) {
            $updatedDish =    tap(Dish::where(["id" => $dish_data["id"]]))->update(
                collect($dish_data)->only([
                    'name',
                    'price',

                    "take_away_discounted_price",
                    "eat_in_discounted_price",
                    "delivery_discounted_price",

                    "order_number",
                    "preparation_time",
                    "take_away",
                    "delivery",
                    "description",
                    "ingredients",
                    "calories"
                ])->all()
            )
                // ->with("somthing")
                ->first();

            if (!empty($dish_data['is_time_based'])) {
                if (empty($dish_data['time_slots'])) {
                    return response(["message" => "time_slots are required"], 400);
                }
                $updatedDish->time_slots()->delete();
                foreach ($dish_data['time_slots'] as $slot) {
                    $updatedDish->time_slots()->create([
                "is_active" => $slot['is_active'],
                        'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                    ]);
                }
            }

            array_push($dishes_array, $updatedDish);
        }




        return response($dishes_array, 200);
    }
    // ##################################################
    // This method is to update dish
    // ##################################################



    /**
     *
     * @OA\Patch(
     *      path="/dishes/Updatedish",
     *      operationId="updateDish2",
     *      tags={"dishes"},

     *      summary="This method is to update dish ",
     *      description="This method is to update dish ",

     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","price","description","id"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="ggg"),
     *            @OA\Property(property="price", type="string", format="string",example="10"),
     *    *  @OA\Property(property="take_away_discounted_price", type="string", format="string",example="12345678"),
     *      *  @OA\Property(property="eat_in_discounted_price", type="string", format="string",example="12345678"),
     *    @OA\Property(property="delivery_discounted_price", type="string", format="string",example="12345678"),
     *      *            @OA\Property(property="order_number", type="string", format="string",example="10"),
     *    *      *            @OA\Property(property="preparation_time", type="string", format="string",example="10"),
     *
     *            @OA\Property(property="description", type="string", format="string",example="Rifat"),
     * *            @OA\Property(property="id", type="number", format="number",example="1"),
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


    public function updateDish2(Request $request)
    {
        // 1. Validate request
        $validated = $request->validate([
            'id' => 'required|integer|exists:dishes,id',
            'menu_id' => 'required|integer|exists:menus,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'delivery' => 'nullable|numeric|min:0',
            'take_away' => 'nullable|numeric|min:0',
            'eat_in_discounted_price' => 'nullable|numeric|min:0',
            'delivery_discounted_price' => 'nullable|numeric|min:0',
            'take_away_discounted_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|string',
            'is_time_based' => 'boolean',
            "show_in_future_date" => 'nullable|boolean',

            'time_slots' => 'nullable|array',
            'time_slots.*.day_of_week' => 'required_with:time_slots|integer|min:0|max:6',
            'time_slots.*.is_active' => 'required_with:time_slots|boolean',
            'time_slots.*.start_time' => 'required_with:time_slots|date_format:H:i:s',
            'time_slots.*.end_time' => 'required_with:time_slots|date_format:H:i:s|after:time_slots.*.start_time',
        ]);

        // 2. Find the dish
        $dish = Dish::findOrFail($validated['id']);

        // 3. Update the dish
        $dish->update(collect($validated)->except(['id', 'time_slots'])->toArray());

        // 4. Handle time slots if time based
        if ($request->boolean('is_time_based') && isset($validated['time_slots'])) {
            // Clear old slots
            $dish->time_slots()->delete();

            // Insert new ones
            foreach ($validated['time_slots'] as $slot) {
                $dish->time_slots()->create($slot);
            }
        }

        // 5. Return updated dish with slots
        return response()->json(
            $dish->load('time_slots'),
            200
        );
    }

    // ##################################################
    // This method is to delete dish
    // ##################################################



    /**
     *
     * @OA\Delete(
     *      path="/dishes/{dishId}",
     *      operationId="deleteDish",
     *      tags={"dishes"},

     *      summary="This method is to delete dish",
     *      description="This method is to delete dish",
     *        @OA\Parameter(
     *         name="dishId",
     *         in="path",
     *         description="dishId",
     *         required=true,
     *      ),

     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(),
     *       ),
     *           @OA\Response(
     *          response=201,
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


    public function deleteDish($dishId, Request $request)
    {
        Dish::where([
            "id" => $dishId,
        ])
            ->delete();

        return response(["message" => "ok"], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\MenuExport;
use App\Http\Utils\DiscountUtil;
use App\Imports\CommonImport;
use App\Imports\MenuImport;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\Variation;
use App\Models\VariationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class MenuController extends Controller
{
    use DiscountUtil;
    // ##################################################
    // This method is to store menu
    // ##################################################

    /**
     *
     * @OA\Post(
     *      path="/menu/csv/{restaurantId}",
     *      operationId="storeMenuByCsv",
     *      tags={"menu"},
     *      summary="This method is to store menu",
     *      description="This method is to store menu",
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *  @OA\RequestBody(
     *   * @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\Schema(
     *         required={"file"},
     *         @OA\Property(
     *             description="file to upload",
     *             property="file",
     *             type="file",
     *             collectionFormat="multi",
     *         )
     *     )
     * )
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


    public function storeMenuByCsv($restaurantId, Request $request)
    {
        $restaurant =  Restaurant::where([
            "id" => $restaurantId
        ])
            ->first();

        if (!$restaurant) {
            return response(["message" => "No Business Found with id " . $restaurantId], 404);
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $data = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Open the file for reading
            $handle = fopen($file->getPathname(), 'r');

            // Initialize an empty array to store the data
            $data = [];

            // Read the header row to use as keys
            $headers = fgetcsv($handle);

            // Read each row from the CSV file
            while (($row = fgetcsv($handle)) !== false) {
                // Combine headers with row values to create associative array
                $data[] = array_combine($headers, $row);
            }

            // Close the file handle
            fclose($handle);

            // Return the data

        }

        foreach ($data as $menuData) {
            if (!empty($menuData["id"]) && !empty($menuData["name"])) {

                $menu = Menu::where([
                    "id" => $menuData["id"]
                ])
                    ->first();

                if ($menu) {
                    $menuExist = Menu::where([
                        "name" =>  $menuData["name"],
                        "restaurant_id" => $restaurantId
                    ])
                        ->whereNotIn("id", [$menu->id])
                        ->first();
                    if ($menuExist) {
                        return response()->json(["message" => "menu already exist by this name"], 409);
                    }
                    $menu->name = $menuData["name"];
                    $menu->save();
                } else {
                    $menuExist = Menu::where([
                        "name" =>  $menuData["name"],
                        "restaurant_id" => $restaurantId
                    ])
                        ->first();
                    if ($menuExist) {
                        return response()->json(["message" => "menu already exist by this name"], 409);
                    }
                    Menu::create(
                        [
                            "name" => $menuData["name"],
                            "restaurant_id" => $restaurantId,
                            "order_number" => $menuData["order_number"],
                            "show_in_customer" => $menuData["show_in_customer"]
                        ]
                    );
                }
            }
        }


        return response()->json(["ok" => true], 200);
    }



    /**
     *
     * @OA\Post(
     *      path="/menu/{restaurantId}",
     *      operationId="storeMenu",
     *      tags={"menu"},

     *      summary="This method is to store menu",
     *      description="This method is to store menu",
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
     *            required={"name","description"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test@g.c"),
     *            @OA\Property(property="description", type="string", format="string",example="12345678"),
     *      *            @OA\Property(property="order_number", type="string", format="string",example="1"),
     *  *      *            @OA\Property(property="show_in_customer", type="string", format="string",example="1"),
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


    public function storeMenu($restaurantId, Request $request)
    {
        $restaurant =  Restaurant::where([
            "id" => $restaurantId
        ])
            ->first();
        if (!$restaurant) {

            return response(["message" => "No Business Found with id " . $restaurantId], 404);
        }

        $body = $request->toArray();
        $body["restaurant_id"] = $restaurantId;

        $menuExist = Menu::where([
            "name" =>  $body["name"],
            "restaurant_id" => $restaurantId
        ])->first();
        if ($menuExist) {
            return response()->json(["message" => "menu already exist by this name"], 409);
        }
        $menu =  Menu::create($body);

        if (!empty($body['is_time_based'])) {
            if (empty($body['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            foreach ($body['time_slots'] as $slot) {

                $menu->time_slots()->create([
                    "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }


        return response($menu, 200);
    }
    public function getRestaurantById($restaurant_id)
    {
        $restaurant = Restaurant::where(["id" => $restaurant_id])->first();
        return $restaurant;
    }
    /**
     *
     * @OA\Post(
     *      path="/menu/check/{restaurantId}",
     *      operationId="checkMenu",
     *      tags={"menu"},

     *      summary="This method is to check menu",
     *      description="This method is to check menu",
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
     *            required={"name"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test@g.c")
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


    public function checkMenu($restaurantId, Request $request)
    {

        $restaurant =   Restaurant::where([
            "id" => $restaurantId
        ])
            ->first();
        if (!$restaurant) {
            return response(["message" => "No Business Found with id " . $restaurantId], 404);
        }

        $body = $request->toArray();
        $body["restaurant_id"] = $restaurantId;

        $menuExist = Menu::where([
            "name" =>  $body["name"],
            "restaurant_id" => $restaurantId
        ])
            ->filter($restaurant)

            ->first();
        if ($menuExist) {
            return response()->json(["data" => true], 200);
        }
        return response()->json(["data" => false], 200);
    }




    // ##################################################
    // This method is to update menu
    // ##################################################

    /**
     *
     * @OA\Patch(
     *      path="/menu/update/{MenuId}",
     *      operationId="updateMenu",
     *      tags={"menu"},

     *      summary="This method is to update menu",
     *      description="This method is to update menu",
     *  @OA\Parameter(
     * name="MenuId",
     * in="path",
     * description="MenuId",
     * required=true,
     * example="1"
     * ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description"},
     *
     *            @OA\Property(property="name", type="string", format="string",example="test@g.c"),
     *            @OA\Property(property="description", type="string", format="string",example="12345678"),
     *            @OA\Property(property="order_number", type="string", format="string",example="1"),
     * *            @OA\Property(property="show_in_customer", type="string", format="string",example="1"),
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






    public function updateMenu($MenuId, Request $request)
    {
        $updatable = Menu::where(["id" => $MenuId])->first();

        if (!$updatable) {
            return response(["message" => "no menu found with id " . $MenuId], 404);
        }

        $menu =    tap(Menu::where(["id" => $MenuId]))->update(
            $request->only(
                'name',
                'description',
                "order_number",
                "show_in_customer",
                "is_time_based"

            )
        )
            ->with("dishes")

            ->first();

        $body = $request->toArray();

        if (!empty($body['is_time_based'])) {
            if (empty($body['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            $menu->time_slots()->delete();
            foreach ($body['time_slots'] as $slot) {

                $menu->time_slots()->create([
                    "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }


        return response($menu, 200);
    }

    // ##################################################
    // This method is to get menu by id
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/menu-dishes-variationtypes-variations",
     *      operationId="getCombinedDataMDVTV",
     *      tags={"menu"},

     *      summary="This method is to get menu by id",
     *      description="This method is to get menu by id",
     *

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


    public function getCombinedDataMDVTV(Request $request)
    {
        $restaurant = Restaurant::where([
            "OwnerID" => auth()->user()->id
        ])
            ->first();

        $data["menu"] = Menu::with("time_slots")
            ->where([
                "restaurant_id" => $restaurant->id
            ])
            ->filter($restaurant)
            ->orderBy("menus.order_number")
            ->get();

        $dishes = Dish::with(
            "menu",
            "dish_variations",
            "dish_variations.variation_type",
            "dish_variations.variation_type.variation",
            "deal.dish",
            "time_slots",
        )
            ->filter($restaurant)
            ->where(function ($query) use ($restaurant) {

                $query->where([
                    "restaurant_id" => $restaurant->id
                ])
                    ->orWhereHas("menu", function ($query) use ($restaurant) {
                        $query->where("menus.restaurant_id", $restaurant->id);
                    });
            })

            ->get();

        foreach ($dishes as $dish) {
            $dish = $this->calculateCampaigns($dish);
        }

        $data["dishes"] = $dishes;
        $data["variation_types"] = VariationType::where([
            "restaurant_id" => $restaurant->id
        ])
            ->get();




        $data["variations"] = Variation::whereHas("variation_type", function ($query) use ($restaurant) {
            $query->where(
                [
                    "variation_types.restaurant_id" => $restaurant->id
                ]
            );
        })
            ->get();

        return response()->json($data, 200);
    }




    /**
     *
     * @OA\Get(
     *      path="/menu/{menuId}",
     *      operationId="getMenuById",
     *      tags={"menu"},

     *      summary="This method is to get menu by id",
     *      description="This method is to get menu by id",
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







    public function getMenuById($menuId, Request $request)
    {

        $menu = Menu::with("dishes", "time_slots")->where([
            "id" => $menuId
        ])

            ->first();


        return response($menu, 200);
    }

    /**
     *
     * @OA\Get(
     *      path="/menu/by-restaurant/{menuId}/{restaurantId}",
     *      operationId="getMenuById2",
     *      tags={"menu"},

     *      summary="This method is to get menu by id",
     *      description="This method is to get menu by id",
     *
     *  *            @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
     *         required=true,
     * example="1"
     *      ),
     *
     *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
     *
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







    public function getMenuById2($menuId, $restaurantId, Request $request)
    {
        $menu = Menu::with("dishes", "time_slots")->where([
            "id" => $menuId,
            "restaurant_id" => $restaurantId
        ])

            ->first();


        return response($menu, 200);
    }


    // ##################################################
    // This method is to get menu by restaurant id
    // ##################################################


    /**
     *
     * @OA\Get(
     *      path="/menu/AllbuId/{restaurantId}",
     *      operationId="getMenuByRestaurantId",
     *      tags={"menu"},

     *      summary="This method is to get menu by restaurant id",
     *      description="This method is to get menu by restaurant id",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
     *   *              @OA\Parameter(
     *         name="response_type",
     *         in="query",
     *         description="response_type: in pdf,csv,json",
     *         required=true,
     *  example="json"
     *      ),
     *      *   *              @OA\Parameter(
     *         name="file_name",
     *         in="query",
     *         description="file_name",
     *         required=true,
     *  example="employee"
     *      ),
     *    *   *              @OA\Parameter(
     *         name="show_in_customer",
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

    public function getMenuByRestaurantId($restaurantId, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $menus = Menu::with("dishes", "time_slots")->where([
            "restaurant_id" => $restaurantId
        ])
            ->filter($restaurant)
            ->when(request()->boolean("show_in_customer"), function ($query) {
                $query->where("show_in_customer", 1);
            })
            ->when(request()->filled("search_key"), function ($query) {
                $term = request("search_key");
                $query->where(function ($q) use ($term) {
                    $q->where("name", "like", "%{$term}%")
                        ->orWhere("description", "like", "%{$term}%");
                });
            })
            ->orderBy("menus.order_number")
            ->get();

        // CHECK IF PDF OR CSV DOWNLOAD
        if (!empty($request->response_type) && in_array(strtoupper($request->response_type), ['PDF', 'CSV'])) {
            if (strtoupper($request->response_type) === 'CSV') {
                return Excel::download(new MenuExport($menus), ((!empty($request->file_name) ? $request->file_name : 'menu') . '.csv'));
            }
        } else {
            return response()->json($menus, 200);
        }
    }


    /**
     *
     * @OA\Get(
     *      path="/menus/all-info/{restaurantId}",
     *      operationId="getMenuWithAllInfoByRestaurantId",
     *      tags={"menu"},

     *      summary="This method is to get menu by restaurant id",
     *      description="This method is to get menu by restaurant id",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     * example="1"
     *      ),
     *
     *   *              @OA\Parameter(
     *         name="response_type",
     *         in="query",
     *         description="response_type: in pdf,csv,json",
     *         required=true,
     *  example="json"
     *      ),
     *      *   *              @OA\Parameter(
     *         name="file_name",
     *         in="query",
     *         description="file_name",
     *         required=true,
     *  example="employee"
     *      ),
     *    *   *              @OA\Parameter(
     *         name="show_in_customer",
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





    public function getMenuWithAllInfoByRestaurantId($restaurantId, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $menus = Menu::with(
            "dishes",
            "dishes.menu",
            "dishes.dish_variations",
            "dishes.dish_variations.variation_type",
            "dishes.dish_variations.variation_type.variation",
            "dishes.deal.dish",
            "time_slots"
        )->where([
            "restaurant_id" => $restaurantId
        ])
            ->filter($restaurant)
            ->when(request()->boolean("show_in_customer"), function ($query) {
                $query->where("show_in_customer", request()->input("show_in_customer"));
            })
            ->orderBy("menus.order_number")
            ->get();

        if (!empty($request->response_type) && in_array(strtoupper($request->response_type), ['PDF', 'CSV'])) {
            if (strtoupper($request->response_type) === 'CSV') {
                return Excel::download(new MenuExport($menus), ((!empty($request->file_name) ? $request->file_name : 'menu') . '.csv'));
            }
            // if (strtoupper($request->response_type) == 'PDF') {
            //     $pdf = PDF::loadView('pdf.menus', ["menus" => $menus]);
            //     return $pdf->download(((!empty($request->file_name) ? $request->file_name : 'employee') . '.pdf'));
            // } elseif (strtoupper($request->response_type) === 'CSV') {
            //     return Excel::download(new MenuExport($menus), ((!empty($request->file_name) ? $request->file_name : 'leave') . '.csv'));
            // }
        } else {
            return response()->json($menus, 200);
        }
    }
    /**
     *
     * @OA\Get(
     *      path="/menu/AllbuId/{restaurantId}/{perPage}",
     *      operationId="getMenuByRestaurantIdWithPagination",
     *      tags={"menu"},

     *      summary="This method is to get menu by restaurant id with pagination",
     *      description="This method is to get menu by restaurant id with pagination",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="restaurantId",
     *         required=true,
     *         example="1"
     *    ),
     *         @OA\Parameter(
     *         name="perPage",
     *         in="path",
     *         description="perPage",
     *         required=true,
     *         example="6"
     *      ),
     *    *   *              @OA\Parameter(
     *         name="show_in_customer",
     *         in="query",
     *         description="",
     *         required=false,
     *  example=""
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





    public function getMenuByRestaurantIdWithPagination($restaurantId, $perPage, Request $request)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $menu = Menu::with("dishes", "time_slots")->where([
            "restaurant_id" => $restaurantId
        ])
            ->filter($restaurant)
            ->when(request()->boolean("show_in_customer"), function ($query) {
                $query->where("show_in_customer", request()->input("show_in_customer"));
            })
            ->orderBy("menus.order_number")
            ->paginate($perPage);

        return response($menu, 200);
    }

    // ##################################################
    // This method is to store multiple menu
    // ##################################################


    /**
     *
     * @OA\Post(
     *      path="/menu/multiple/{restaurantId}",
     *      operationId="storeMultipleMenu",
     *      tags={"menu"},

     *      summary="This method is to store multiple menu",
     *      description="This method is to store multiple menu",
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
     *            required={"menu"},


     *  @OA\Property(property="menu", type="string", format="array",example={

     *  {	"name":"hggggg","description":555,"order_number":1,"show_in_customer":0},
     *  {	"name":"hggggg","description":555,"order_number":1,"show_in_customer":0},
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
    public function storeMultipleMenu($restaurantId, Request $request)
    {


        $duplicate_indexes_array = [];
        $uniqueMenus = collect($request->menu)->unique('name');
        $uniqueMenus->values()->all();
        foreach ($uniqueMenus as $index => $menu) {
            $typeFound =    Menu::where(["restaurant_id" => $restaurantId, "name" => $menu["name"]])
                ->first();

            if ($typeFound) {

                array_push($duplicate_indexes_array, $index);
            }
        }

        if (count($duplicate_indexes_array)) {

            return response([
                "message" => "duplicate data",
                "duplicate_indexes_array" => $duplicate_indexes_array
            ], 409);
        } else {
            $menus_array = [];
            foreach ($uniqueMenus  as $body) {
                $body["restaurant_id"] = $restaurantId;
                $menu =  Menu::create($body);

                if (!empty($body['is_time_based'])) {
                    if (empty($body['time_slots'])) {
                        return response(["message" => "time_slots are required"], 400);
                    }
                    foreach ($body['time_slots'] as $slot) {

                        $menu->time_slots()->create([
                            "is_active" => $slot['is_active'],
                            'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                            'start_time'  => $slot['start_time'],
                            'end_time'    => $slot['end_time'],
                        ]);
                    }
                }
                array_push($menus_array, $menu);
            }
        }


        return response($menus_array, 201);
    }

    // ##################################################
    // This method is to update multiple menu
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/menu/Edit/multiple",
     *      operationId="updateMultipleMenu",
     *      tags={"menu"},
     *      summary="This method is to update multiple menu",
     *      description="This method is to update multiple menu",
     *
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"menu","restaurant_id"},

     *  @OA\Property(property="restaurant_id", type="string", format="array",example="1"
     * ),
     *  @OA\Property(property="menu", type="string", format="array",example={

     *  {"id":"1",	"name":"hggggg","description":555},
     *  {"id":"2",	"name":"hggggg","description":555},
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

    public function updateMultipleMenu(Request $request)
    {

        $menus = $request->menu;
        $duplicate_indexes_array = [];
        $uniqueMenus = collect($request->menu)->unique('name');
        $uniqueMenus->values()->all();




        foreach ($uniqueMenus as $index => $menu) {

            $menu =    Menu::where(["id" => $menu["id"]])
                ->first();
            if (!$menu) {
                return response()->json(
                    [
                        "message" => ("menu not found at position" . $index)
                    ],
                    422
                );
            }

            $typeFound =    Menu::where(["restaurant_id" => $request->restaurant_id, "name" => $menu["name"]])
                ->whereNotIn("id", [$menu->id])
                ->first();

            if ($typeFound) {
                array_push($duplicate_indexes_array, $index);
            }
        }

        if (count($duplicate_indexes_array)) {

            return response([
                "message" => "duplicate data",
                "duplicate_indexes_array" => $duplicate_indexes_array
            ], 409);
        } else {
            $menus_array = [];

            foreach ($uniqueMenus as $body) {
                $menu = Menu::where(["id" => $body["id"]])->first();

                if (!$menu) {
                    return response(["message" => "no menu found with id " . $body["id"]], 404);
                }
                $menu =    tap(Menu::where(["id" => $body["id"]]))->update(
                    collect($body)->only([
                        'name',
                        'description',
                        "icon",
                        "order_number",
                        "show_in_customer",
                        "is_time_based"

                    ])->all()
                )
                    ->with("dishes")
                    ->first();

                if (!empty($body['is_time_based'])) {
                    if (empty($body['time_slots'])) {
                        return response(["message" => "time_slots are required"], 400);
                    }
                    $menu->time_slots()->delete();
                    foreach ($body['time_slots'] as $slot) {

                        $menu->time_slots()->create([
                            "is_active" => $slot['is_active'],
                            'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                            'start_time'  => $slot['start_time'],
                            'end_time'    => $slot['end_time'],
                        ]);
                    }
                }

                array_push($menus_array, $menu);
            }
            return response($menus, 200);
        }
    }
    // ##################################################
    // This method is to update  menu2
    // ##################################################


    /**
     *
     * @OA\Patch(
     *      path="/menu/Updatemenu",
     *      operationId="updateMenu2",
     *      tags={"menu"},

     *      summary="This method is to update menu2",
     *      description="This method is to update menu2",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id","name","description"},
     *             @OA\Property(property="id", type="number", format="number",example="1"),
     *             @OA\Property(property="name", type="string", format="string",example="test@g.c"),
     *            @OA\Property(property="description", type="string", format="string",example="12345678")
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




    public function updateMenu2(Request $request)
    {
        $updatable = Menu::where(["id" => $request->id])->first();

        if (!$updatable) {
            return response(["message" => "no menu found with id " . $request->id], 404);
        }

        $menu =    tap(Menu::with("dishes")->where(["id" => $request->id]))->update(
            $request->only(
                'name',
                'description'
            )
        )


            ->first();

        $body = $request->toArray();
        if (!empty($body['is_time_based'])) {
            if (empty($body['time_slots'])) {
                return response(["message" => "time_slots are required"], 400);
            }
            $menu->time_slots()->delete();
            foreach ($body['time_slots'] as $slot) {

                $menu->time_slots()->create([
                    "is_active" => $slot['is_active'],
                    'day_of_week' => $slot['day_of_week'], // 0 = Sunday ... 6 = Saturday
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }


        return response($menu, 200);
    }

    // ##################################################
    // This method is to delete menu
    // ##################################################

    /**
     *
     * @OA\Delete(
     *      path="/menu/{menuId}",
     *      operationId="deleteMenu",
     *      tags={"menu"},

     *      summary="This method is to delete menu",
     *      description="This method is to delete menu",
     *        @OA\Parameter(
     *         name="menuId",
     *         in="path",
     *         description="menuId",
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

    public function deleteMenu($menuId, Request $request)
    {
        Menu::where([
            "id" => $menuId,
        ])
            ->delete();



        return response(["message" => "ok"], 200);
    }
}

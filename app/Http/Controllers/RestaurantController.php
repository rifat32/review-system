<?php

namespace App\Http\Controllers;

use App\Exports\BusinessExport;
use App\Models\DailyView;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Question;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\Review;
use App\Models\ReviewNew;
use App\Models\ReviewValue;
use App\Models\ReviewValueNew;
use App\Models\Star;
use App\Models\Tag;
use App\Models\User;
use App\Models\VariationType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class RestaurantController extends Controller
{

    // ##################################################
    // This method is to store restaurant
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/restaurant",
     *      operationId="storeRestaurent",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store restaurant",
     *      description="This method is to store restaurant",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"Name","Address","PostCode","enable_question"},
     *             @OA\Property(property="Name", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="Address", type="string", format="string",example="How was this?"),
     *
     *
     *            @OA\Property(property="PostCode", type="string", format="string",example="How was this?"),

     * *  @OA\Property(property="enable_question", type="boolean", format="boolean",example="1"),
     *   *  *  *   *               @OA\Property(property="is_eat_in", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_delivery", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_take_away", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_customer_order", type="string", format="string",example="0")
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

    public function storeRestaurent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Name' => 'required|unique:businesses,Name',
            'Address' => 'required|string',
            'PostCode' => 'required',
            'enable_question' => 'required',

            'is_eat_in' => 'nullable',
            'is_delivery' => 'nullable',
            'is_take_away' => 'nullable',
            'is_customer_order' => 'nullable',


        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 422]);
        }
        $validatedData = $validator->validated();
        $validatedData["OwnerID"] = $request->user()->id;
        $validatedData["Status"] = "Inactive";

        $validatedData["Key_ID"] = Str::random(10);
        $validatedData["expiry_date"] = Date('y:m:d', strtotime('+15 days'));



        $validatedData["eat_in_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];
        $validatedData["takeaway_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];
        $validatedData["delivery_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];



        $restaurant =  Restaurant::create($validatedData);


        return response($restaurant, 200);
    }
    // ##################################################
    // This method is to store restaurant
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/restaurant/by-owner-id",
     *      operationId="storeRestaurentByOwnerId",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store restaurant by owner id",
     *      description="This method is to store restaurant by owner id",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"Name","Address","PostCode","enable_question"},
     *             @OA\Property(property="Name", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="Address", type="string", format="string",example="How was this?"),

     *
     *            @OA\Property(property="PostCode", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="OwnerID", type="string", format="string",example="How was this?"),
     *

     * *  @OA\Property(property="enable_question", type="boolean", format="boolean",example="1"),
     *  *  *  *   *               @OA\Property(property="is_eat_in", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_delivery", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_take_away", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_customer_order", type="string", format="string",example="0")
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

    public function storeRestaurentByOwnerId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Name' => 'required|unique:businesses,Name',
            'Address' => 'required|string',
            'PostCode' => 'required',
            'OwnerID' => 'required',
            'enable_question' => 'required',

            'is_eat_in' => 'nullable',
            'is_delivery' => 'nullable',
            'is_take_away' => 'nullable',
            'is_customer_order' => 'nullable',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 422]);
        }
        $validatedData = $validator->validated();

        $validatedData["Status"] = "Inactive";

        $validatedData["Key_ID"] = Str::random(10);
        $validatedData["expiry_date"] = Date('y:m:d', strtotime('+15 days'));

        $validatedData["eat_in_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];
        $validatedData["takeaway_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];
        $validatedData["delivery_payment_mode"] = [
            "cash" => 1,
            "stripe" => 0
        ];




        $restaurant =  Restaurant::create($validatedData);


        return response($restaurant, 200);
    }
    /**
     *
     * @OA\Delete(
     *      path="/restaurant/delete/{id}",
     *      operationId="deleteRestaurantByRestaurentId",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="id",
     * in="path",
     * description="id",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to delete restaurant by id",
     *      description="This method is to delete restaurant by id",
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

    public function deleteRestaurantByRestaurentId($id, Request $request)
    {

        if (!$request->user()->hasRole("superadmin")) {
            return response()->json(["message" => "You do not have permission", 401]);
        }



        Restaurant::where(["id" => $id])->delete();



        DailyView::where(["restaurant_id" => $id])->delete();



        return response(["ok" => true], 200);
    }

    /**
     *
     * @OA\Delete(
     *      path="/restaurant/delete/force-delete/{email}",
     *      operationId="deleteRestaurantByRestaurentIdForceDelete",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="email",
     * in="path",
     * description="email",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to delete restaurant by id",
     *      description="This method is to delete restaurant by id",
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

    public function deleteRestaurantByRestaurentIdForceDelete($email, Request $request)
    {

        if (!$request->user()->hasRole("superadmin")) {
            return response()->json(["message" => "You do not have permission", 401]);
        }



        $restaurant = Restaurant::where(["EmailAddress" => $email])->first();
        $id = $restaurant->id;

        if ($restaurant && $restaurant->created_at >= Carbon::now()->subMinutes(5)) {
            $restaurant->forceDelete();
            User::where([
                "id" => $restaurant->OwnerID
            ])
                ->delete();
            DailyView::where(["restaurant_id" => $id])->delete();
            Dish::where(["restaurant_id" => $id])->delete();
            Menu::where(["restaurant_id" => $id])->delete();
            Notification::where(["restaurant_id" => $id])->delete();
            Order::where(["restaurant_id" => $id])->delete();
            Question::where(["restaurant_id" => $id])->delete();
            RestaurantTable::where(["restaurant_id" => $id])->delete();
            Review::where(["restaurant_id" => $id])->delete();
            ReviewNew::where(["restaurant_id" => $id])->delete();
            ReviewValue::where(["restaurant_id" => $id])->delete();
            Tag::where(["restaurant_id" => $id])->delete();
            VariationType::where(["restaurant_id" => $id])->delete();
        }

        return response(["ok" => true], 200);
    }


    // ##################################################
    // This method is to upload restaurant image
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/restaurant/uploadimage/{restaurentId}",
     *      operationId="uploadRestaurentImage",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to upload restaurant image",
     *      description="This method is to upload restaurant image",
     *        @OA\Parameter(
     *         name="restaurentId",
     *         in="path",
     *         description="restaurent Id",
     *         required=false,
     *      ),
     *            @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         description="method",
     *         required=false,
     * example="PATCH"
     *      ),
     *
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="logo",
     *                     type="file",
     *                ),
     *                 required={"logo"}
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
     * @OA\JsonContent()
     * ),
     *  * @OA\Response(
     *      response=400,
     *      description="Bad Request",
     * @OA\JsonContent()
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found",
     *     @OA\JsonContent()
     *   )

     *      )
     *     )
     */
    public function uploadRestaurentImage($restaurentId, Request $request)
    {

        $request->validate([

            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $checkRestaurant =    Restaurant::where(["id" => $restaurentId])->first();
        if ($checkRestaurant->OwnerID != $request->user()->id && !$request->user()->hasRole("superadmin")) {
            return response()->json(["message" => "This is not your business", 401]);
        }


        $imageName = time() . '.' . $request->logo->extension();



        $request->logo->move(public_path('img/restaurant'), $imageName);

        $imageName = "img/restaurant/" . $imageName;

        $data["restaurent"] =    tap(Restaurant::where(["id" => $restaurentId]))->update([
            "Logo" => $imageName
        ])
            // ->with("somthing")

            ->first();


        if (!$data["restaurent"]) {
            return response()->json(["message" => "No User Found"], 404);
        }

        $data["message"] = "restaurant image updates successfully";
        return response()->json($data, 200);
    }
    // ##################################################
    // This method is to update restaurant details
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/restaurant/UpdateResturantDetails/{restaurentId}",
     *      operationId="UpdateResturantDetails",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update restaurant",
     *      description="This method is to update restaurant",
     *
     *  *            @OA\Parameter(
     *         name="restaurentId",
     *         in="path",
     *         description="method",
     *         required=true,
     * example="1"
     *      ),
     *
     *            @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         description="method",
     *         required=true,
     * example="PATCH"
     *      ),
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={ "Name","Layout","Address","PostCode", "enable_question" , "totalTables"},
     *
     *                 @OA\Property(property="GoogleMapApi", type="string", format="string",example="restaurant name"),
     *
     *                 @OA\Property(property="Name", type="string", format="string",example="restaurant name"),
     *
     *                 @OA\Property(property="totalTables", type="number", format="number",example="1"),

     *
     *
     *
     *                @OA\Property(property="EmailAddress", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="homeText", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="AdditionalInformation", type="string", format="string",example="How was this?"),
     *
     *
     *             @OA\Property(property="PostCode", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="Webpage", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="PhoneNumber", type="string", format="string",example="How was this?"),
     *
     *
     *             @OA\Property(property="About", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="Layout", type="string", format="string",example="How was this?"),
     *            @OA\Property(property="Address", type="string", format="string",example="How was this?"),

     *

     * *  @OA\Property(property="enable_question", type="boolean", format="boolean",example="1"),
     *  *  *  *   *               @OA\Property(property="is_eat_in", type="string", format="string",example="0"),
     *      *  *  *  *   *               @OA\Property(property="tax_percentage", type="string", format="string",example="0"),
     *    *      *  *  *  *   *               @OA\Property(property="show_image", type="string", format="string",example="0"),
     *


     *  *  *   *               @OA\Property(property="is_delivery", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_take_away", type="string", format="string",example="0"),
     *  *  *   *               @OA\Property(property="is_customer_order", type="string", format="string",example="0"),
     *     *  *  *   *               @OA\Property(property="Key_ID", type="string", format="string",example="0"),
     *  *     *  *  *   *               @OA\Property(property="review_type", type="string", format="string",example="0"),
     *     @OA\Property(property="google_map_iframe", type="string", format="string",example="test"),
     *
     *  *  *        @OA\Property(property="Is_guest_user", type="boolean", format="boolean",example="false"),
     *  *        @OA\Property(property="is_review_silder", type="boolean", format="boolean",example="false"),
     *     *    *   *  *        @OA\Property(property="is_business_type_restaurant", type="boolean", format="boolean",example="true"),
     *   *   *    *   *  *        @OA\Property(property="business_type", type="string", format="string",example="restaurant"),
     *
     *     *   *   *    *   *  *        @OA\Property(property="header_image", type="string", format="string",example="/header_image/default.png"),
     *
     *     * *     *   *   *    *   *  *        @OA\Property(property="menu_pdf", type="string", format="string",example="/menu_pdf/default.pdf"),
     *
     *
     *    *   *  *        @OA\Property(property="is_pdf_manu", type="boolean", format="boolean",example="true"),
     *
     *  *    *   *  *        @OA\Property(property="primary_color", type="string", format="string",example="red"),
     *  *  *    *   *  *        @OA\Property(property="secondary_color", type="string", format="string",example="red"),
     * *  *  *  *    *   *  *        @OA\Property(property="client_primary_color", type="string", format="string",example="red"),
     *
     *  *  *  *    *   *  *        @OA\Property(property="client_secondary_color", type="string", format="string",example="red"),
     *
     *  *  *  *    *   *  *        @OA\Property(property="client_tertiary_color", type="string", format="string",example="red"),
     *         @OA\Property(property="user_review_report", type="boolean", format="boolean",example="1"),
     *  *       @OA\Property(property="guest_user_review_report", type="boolean", format="boolean",example="1"),
     *
     *   * *  *       @OA\Property(property="is_customer_order_enabled", type="boolean", format="boolean",example="1"),
     *    *   * *  *       @OA\Property(property="is_report_email_enabled", type="boolean", format="boolean",example="1"),
     *
     *
     *     *  *       @OA\Property(property="pin", type="string", format="string",example="1"),
     * *     *  *       @OA\Property(property="enable_customer_order_payment", type="boolean", format="boolean",example="1"),

     *                 @OA\Property(property="eat_in_payment_mode", type="string", format="array",example={
     *  "cash":0,
     *  "stripe":0
     * }),
     *   *                 @OA\Property(property="takeaway_payment_mode", type="string", format="array",example={
     *  "cash":0,
     *  "stripe":0
     * }),
     *   *                 @OA\Property(property="delivery_payment_mode", type="string", format="array",example={
     *  "cash":0,
     *  "stripe":0
     * }),
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

    public function UpdateResturantDetails($restaurentId, Request $request)
    {


        $checkRestaurant =    Restaurant::where(["id" => $restaurentId])->first();

        if ($checkRestaurant->OwnerID != $request->user()->id && !$request->user()->hasRole("superadmin")) {
            return response()->json(["message" => "This is not your restaurant", 401]);
        }

        $data["restaurant"] =    tap(Restaurant::where(["id" => $restaurentId]))->update($request->only(
            "show_image",
            "tax_percentage",
            "review_type",
            "google_map_iframe",
            "Key_ID",
            "Status",
            "About",
            "Name",
            "Layout",
            "Address",
            "PostCode",
            "enable_question",
            "Webpage",
            "PhoneNumber",
            "EmailAddress",
            "homeText",
            "AdditionalInformation",
            "totalTables",
            "GoogleMapApi",

            'is_eat_in',
            'is_delivery',
            'is_take_away',
            'is_customer_order',
            'Is_guest_user',
            'is_review_silder',
            "review_only",
            "is_business_type_restaurant",
            "header_image",
            "menu_pdf",
            "business_type",
            "is_pdf_manu",

            "primary_color",
            "secondary_color",

            "client_primary_color",
            "client_secondary_color",
            "client_tertiary_color",
            "user_review_report",
            "guest_user_review_report",


            "pin",


            "is_customer_order_enabled",
            "is_report_email_enabled",

            "enable_customer_order_payment",

            "eat_in_payment_mode",
            "takeaway_payment_mode",
            "delivery_payment_mode",
            "is_customer_schedule_order",
            "time_zone"

        ))
            // ->with("somthing")

            ->first();


        if (!$data["restaurant"]) {
            return response()->json(["message" => "No Business Found"], 404);
        }


        $data["message"] = "Restaurant updates successfully";
        return response()->json($data, 200);
    }
    // ##################################################
    // This method is to update restaurant details by admin
    // ##################################################
    public function UpdateResturantDetailsByAdmin($restaurentId, Request $request)
    {
        $checkRestaurant =    Restaurant::where(["id" => $restaurentId])->first();
        if ($checkRestaurant->OwnerID != $request->user()->id && !$request->user()->hasRole("superadmin")) {
            return response()->json(["message" => "This is not your restaurant", 401]);
        }

        $data["restaurant"] =    tap(Restaurant::where(["id" => $restaurentId]))->update($request->only(
            "show_image",
            "tax_percentage",
            "review_type",
            "google_map_iframe",
            "Key_ID",
            "Status",
            "About",
            "Name",
            "Layout",
            "Address",
            "PostCode",
            "enable_question",
            "Webpage",
            "PhoneNumber",
            "EmailAddress",
            "homeText",
            "AdditionalInformation",
            "totalTables",
            "GoogleMapApi",

            'is_eat_in',
            'is_delivery',
            'is_take_away',
            'is_customer_order',
            'Is_guest_user',
            'is_review_silder',
            "review_only",
            "is_business_type_restaurant",
            "header_image",
            "menu_pdf",
            "business_type",
            "is_pdf_manu",

            "primary_color",
            "secondary_color",

            "client_primary_color",
            "client_secondary_color",
            "client_tertiary_color",
            "user_review_report",
            "guest_user_review_report",
            "is_customer_schedule_order",
            "time_zone"
        ))
            // ->with("somthing")

            ->first();


        if (!$data["restaurant"]) {
            return response()->json(["message" => "No Business Found"], 404);
        }


        $data["message"] = "Restaurant updates successfully";
        return response()->json($data, 200);
    }
    // ##################################################
    // This method is to get restaurant by id
    // ##################################################
    /**
     *
     * @OA\Get(
     *      path="/restaurant/{restaurantId}",
     *      operationId="getrestaurantById",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get restaurant by id",
     *      description="This method is to get restaurant by id",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="method",
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
    public function getrestaurantById($restaurantId)
    {
        $data["restaurant"] =   Restaurant::with([
            "owner"
        ])
        
        ->where(["id" => $restaurantId])->first();
        $data["ok"] = true;

        if (!$data["restaurant"]) {
            return response(["message" => "No Business Found"], 404);
        }
        return response($data, 200);
    }
    // ##################################################
    // This method is to get restaurant all
    // ##################################################
    // ##################################################
    // This method is to get restaurant by id
    // ##################################################
    /**
     *
     * @OA\Get(
     *      path="/restaurant",
     *      operationId="getAllRestaurants",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },

     *    *  @OA\Parameter(
     * name="search_key",
     * in="query",
     * description="search_key",
     * required=true,
     * example="restaurant name"
     * ),
     *
     *
     *      summary="This method is to get all restaurant ",
     *      description="This method is to get all restaurant ",
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
    public function getAllRestaurants(Request $request)
    {


        $restaurantQuery =  Restaurant::with("owner");

        if (!empty($request->search_key)) {
            $restaurantQuery = $restaurantQuery->where(function ($query) use ($request) {
                $term = $request->search_key;
                $query->where("Name", "like", "%" . $term . "%");
            });
        }



        $data["restaurant"] =   $restaurantQuery->get();
        $data["ok"] = true;






        //         if(!$data["restaurant"]) {
        //   return response([ "message" => "No Business Found"], 404);
        //         }
        return response($data, 200);
    }
    /**
     *
     * @OA\Get(
     *      path="/restaurants/{perPage}",
     *      operationId="getRestaurants",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *  *   *              @OA\Parameter(
     *         name="response_type",
     *         in="query",
     *         description="response_type: in pdf,csv,json",
     *         required=true,
     *  example="json"
     *      ),
     *    *  @OA\Parameter(
     * name="perPage",
     * in="path",
     * description="perPage",
     * required=true,
     * example="10"
     * ),
     *    *  @OA\Parameter(
     * name="search_key",
     * in="query",
     * description="search_key",
     * required=true,
     * example="restaurant name"
     * ),
     *
     *
     *      summary="This method is to get all restaurant ",
     *      description="This method is to get all restaurant ",
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
    public function getRestaurants($perPage, Request $request)
    {

        //  customers

        $restaurantQuery =  Restaurant::withCount(
                "menus",
                "dishes",
                "customers"
            )
            ->with(
                "owner",

            )
            ->when(request()->filled("Status"), function ($query) {
                $query->where("Status", request()->input("Status"));
            })
            ->when(request()->filled("review_only"), function ($query) {
                $query->where("review_only", request()->input("review_only"));
            })
            ->when(request()->filled("is_customer_order"), function ($query) {
                $query->where("is_customer_order", request()->input("is_customer_order"));
            });

        if (!empty($request->search_key)) {
            $restaurantQuery = $restaurantQuery->where(function ($query) use ($request) {
                $term = $request->search_key;
                $query->where("Name", "like", "%" . $term . "%")
                    ->orWhere("Address", "like", "%" . $term . "%")
                    ->orWhere("PostCode", "like", "%" . $term . "%")
                    ->orWhere("Status", "like", "%" . $term . "%")
                    ->orWhere("About", "like", "%" . $term . "%")
                    ->orWhere("PhoneNumber", "like", "%" . $term . "%")
                    ->orWhere("EmailAddress", "like", "%" . $term . "%");
            });
        }




        $businesses =   $restaurantQuery->paginate($perPage);

        if (!empty($request->response_type) && in_array(strtoupper($request->response_type), ['PDF', 'CSV'])) {
            if (strtoupper($request->response_type) == 'PDF') {
                if (empty($businesses->count())) {
                    $pdf = PDF::loadView('pdf.no_data', []);
                } else {
                    $pdf = PDF::loadView('pdf.businesses', ["businesses" => $businesses]);
                }

                return $pdf->download(((!empty($request->file_name) ? $request->file_name : 'attendance') . '.pdf'));
            } elseif (strtoupper($request->response_type) === 'CSV') {

                return Excel::download(new BusinessExport($businesses), ((!empty($request->file_name) ? $request->file_name : 'businesses') . '.csv'));
            }
        } else {
            return response()->json($businesses, 200);
        }
    }

    /**
     *
     * @OA\Get(
     *      path="/client/restaurants/{perPage}",
     *      operationId="getRestaurantsClients",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *    *  @OA\Parameter(
     * name="perPage",
     * in="path",
     * description="perPage",
     * required=true,
     * example="10"
     * ),
     *    *  @OA\Parameter(
     * name="sort_by",
     * in="query",
     * description="sort_by",
     * required=true,
     * example="sort_by"
     * ),
     *    *  @OA\Parameter(
     * name="sort_type",
     * in="query",
     * description="sort_type",
     * required=true,
     * example="sort_type"
     * ),


     *
     *
     *      summary="This method is to get all restaurant ",
     *      description="This method is to get all restaurant ",
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
    public function getRestaurantsClients($perPage, Request $request)
    {

        // Get today's day (0 for Sunday, 1 for Monday, ..., 6 for Saturday)
        $today = Carbon::now()->dayOfWeek;

        $restaurantQuery = Restaurant::when((!empty($request->sort_type) && !empty($request->sort_by)), function ($query) use ($request) {
            $query->orderBy($request->sort_by, $request->sort_type);
        });



        if (!empty($request->search_key)) {
            $restaurantQuery->where(function ($query) use ($request) {
                $term = $request->search_key;
                $query->where("Name", "like", "%" . $term . "%");
            });
        }

        $businesses = $restaurantQuery
            ->select(
                "id",
                "Name",
                "header_image",
                "rating_page_image",
                "placeholder_image",
                "Logo",
                "Address"
            )

            ->paginate($perPage);


      $businesses->getCollection()->transform(function ($business) use ($today, $request) {
    $totalCount = 0;
    $totalRating = 0;

    foreach (Star::get() as $star) {
        $selectedCount = ReviewValueNew::leftjoin('review_news', 'review_value_news.review_id', '=', 'review_news.id')
            ->where([
                "review_news.restaurant_id" => $business->id,
                "star_id" => $star->id,
            ])
            ->distinct("review_value_news.review_id", "review_value_news.question_id");

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $selectedCount = $selectedCount->whereBetween('review_news.created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $selectedCount = $selectedCount->count();

        $totalCount += $selectedCount * $star->value;
        $totalRating += $selectedCount;
    }

    $average_rating = $totalCount > 0 ? $totalCount / $totalRating : 0;

    $timing = $business->times()->with("timeSlots")->where('day', $today)->first();

    $business->average_rating = $average_rating;
    $business->total_rating_count = $totalCount;
    $business->out_of = 5;
    $business->timing = $timing;

    return $business;
});

return response()->json($businesses, 200);

  
    }


    // ##################################################
    // This method is to get restaurant table by restaurant id
    // ##################################################
    /**
     *
     * @OA\Get(
     *      path="/restaurant/Restuarant/tables/{restaurantId}",
     *      operationId="getrestaurantTableByRestaurantId",
     *      tags={"restaurant"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get restaurant with table by restaurant id",
     *      description="This method is to get restaurant with table by restaurant id",
     *
     *  *            @OA\Parameter(
     *         name="restaurantId",
     *         in="path",
     *         description="method",
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

    public function getrestaurantTableByRestaurantId($restaurantId)
    {
        $data["restaurant"] =   Restaurant::with("owner", "table")->where(["id" => $restaurantId])->first();
        $data["ok"] = true;

        if (!$data["restaurant"]) {
            return response(["message" => "No Business Found"], 404);
        }
        return response($data, 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\RestaurantOrderPartner;
use Illuminate\Http\Request;

class RestaurantOrderPartnerController extends Controller
{
  /**
        *
     * @OA\Post(
     *      path="/order/restaurant-partner/create",
     *      operationId="createRestaurantOrderPartner",
     *      tags={"restaurant-partner"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store dashboard widget",
     *      description="This method is to store single dashboard widget",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={"name","description","user_type"},
     *
     *             @OA\Property(property="restaurant_partner_id", type="string", format="string",example="test"),
     *            @OA\Property(property="delivery", type="string", format="string",example="12345678"),
     *            @OA\Property(property="delivery_order_commission", type="string", format="string",example="admin"),
 *            @OA\Property(property="delivery_shop_link", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in_order_commission", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in_shop_link", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="takeaway", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="takeaway_order_commission", type="string", format="string",example="admin"),
 *  *  *            @OA\Property(property="takeaway_link", type="string", format="string",example="admin"),
 *  *  *            @OA\Property(property="contact_details", type="string", format="string",example="admin"),
 *  *  *  *  *            @OA\Property(property="api_key", type="string", format="string",example="api_key"),
 *  *  *  *  *            @OA\Property(property="payment_terms", type="string", format="string",example="payment_terms"),
 *
 *  *  *  *  *  *            @OA\Property(property="is_active", type="string", format="string",example="is_active"),
 * *  *  *            @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
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


     public function createRestaurantOrderPartner (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  RestaurantOrderPartner::create($body);
        return response($dashboard_widget, 200);
    }

     /**
        *
     * @OA\Put(
     *      path="/order/restaurant-partner/update",
     *      operationId="updateRestaurantOrderPartner",
     *      tags={"restaurant-partner"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update dashboard widget",
     *      description="This method is to update single dashboard widget",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={"id","name","description","user_type"},
     *             @OA\Property(property="id", type="string", format="string",example="1"),
     *
       *             @OA\Property(property="restaurant_partner_id", type="string", format="string",example="test"),
     *            @OA\Property(property="delivery", type="string", format="string",example="12345678"),
     *            @OA\Property(property="delivery_order_commission", type="string", format="string",example="admin"),
 *            @OA\Property(property="delivery_shop_link", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in_order_commission", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="eat_in_shop_link", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="takeaway", type="string", format="string",example="admin"),
 *  *            @OA\Property(property="takeaway_order_commission", type="string", format="string",example="admin"),
 *  *  *            @OA\Property(property="takeaway_link", type="string", format="string",example="admin"),
 *  *  *            @OA\Property(property="contact_details", type="string", format="string",example="admin"),
 *  *  *  *            @OA\Property(property="api_key", type="string", format="string",example="api_key"),
 *  *  *  *  *            @OA\Property(property="payment_terms", type="string", format="string",example="payment_terms"),
 *
 *  *  *  *  *  *            @OA\Property(property="is_active", type="string", format="string",example="is_active"),
*
 *  * *  *  *            @OA\Property(property="restaurant_id", type="string", format="string",example="1"),

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


    public function updateRestaurantOrderPartner (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  tap(RestaurantOrderPartner::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Get(
     *      path="/order/restaurant-partner/get-all/{restaurant_id}",
     *      operationId="getRestaurantOrderPartner",
     *      tags={"restaurant-partner"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *        *  @OA\Parameter(
* name="restaurant_id",
* in="path",
* description="restaurant_id",
* required=true,
* example="1"
* ),
     *      summary="This method is to get dashboard widget",
     *      description="This method is to get  dashboard widget",
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

    public function getRestaurantOrderPartner ($restaurant_id,Request $request) {
        $dashboard_widget =  RestaurantOrderPartner::
        with("restaurant_partner")
        ->where([
            "restaurant_id" => $restaurant_id
        ])
        ->get();
        return response($dashboard_widget, 200);
    }

     /**
        *
     * @OA\Get(
     *      path="/order/restaurant-partner/get/{id}",
     *      operationId="getRestaurantOrderPartnerById",
     *      tags={"restaurant-partner"},
     *      security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
* name="id",
* in="path",
* description="id",
* required=true,
* example="1"
* ),
     *      summary="This method is to get dashboard widget by id",
     *      description="This method is to get single dashboard widget by id",
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

    public function getRestaurantOrderPartnerById ($id, Request $request) {
        $dashboard_widget =  RestaurantOrderPartner::where([
            "id" => $id
        ])->first();
        return response($dashboard_widget, 200);
    }

/**
        *
     * @OA\Delete(
     *      path="/order/restaurant-partner/delete/{id}",
     *      operationId="deleteRestaurantOrderPartnerById",
     *      tags={"restaurant-partner"},
     *        security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
* name="id",
* in="path",
* description="id",
* required=true,
* example="1"
* ),
     *      summary="This method is to delete dashboard widget by id",
     *      description="This method is to delete single dashboard widget by id",
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

    public function deleteRestaurantOrderPartnerById ($id, Request $request) {
        RestaurantOrderPartner::where([
            "id" => $id
        ])->delete();
        return response(["success"=>true], 200);
    }

}

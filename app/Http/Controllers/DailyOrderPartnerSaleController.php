<?php

namespace App\Http\Controllers;

use App\Models\DailyOrderPartnerSale;
use Illuminate\Http\Request;

class DailyOrderPartnerSaleController extends Controller
{
     /**
        *
     * @OA\Post(
     *      path="/order/daily-order-partner-sale/create",
     *      operationId="createDailyOrderPartnerSale",
     *      tags={"daily-order-partner-sale"},
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
   * @OA\Property(property="restaurant_partner_id", type="string", format="string", example="Partner Name"),
 * @OA\Property(property="eat_in_orders", type="integer", format="int32", example=10),
 * @OA\Property(property="eat_in_orders_amount", type="number", format="double", example=150.50),
 * @OA\Property(property="takeaway_orders", type="integer", format="int32", example=5),
 * @OA\Property(property="takeaway_orders_amount", type="number", format="double", example=75.25),
 * @OA\Property(property="notes", type="string", format="string", example="Some notes"),
 * @OA\Property(property="bank_payment", type="number", format="double", example=100.00),
 * @OA\Property(property="cash_payment", type="number", format="double", example=50.50),
 * @OA\Property(property="delivery_orders", type="integer", format="int32", example=8),
 *  * @OA\Property(property="delivery_orders_amount", type="integer", format="int32", example=8),
 *
 * @OA\Property(property="restaurant_id", type="integer", format="int64", example=1),

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


     public function createDailyOrderPartnerSale (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  DailyOrderPartnerSale::create($body);
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Put(
     *      path="/order/daily-order-partner-sale/update",
     *      operationId="updateDailyOrderPartnerSale",
     *      tags={"daily-order-partner-sale"},
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
 * @OA\Property(property="restaurant_partner_id", type="string", format="string", example="Partner Name"),
 * @OA\Property(property="eat_in_orders", type="integer", format="int32", example=10),
 * @OA\Property(property="eat_in_orders_amount", type="number", format="double", example=150.50),
 * @OA\Property(property="takeaway_orders", type="integer", format="int32", example=5),
 * @OA\Property(property="takeaway_orders_amount", type="number", format="double", example=75.25),
 * @OA\Property(property="notes", type="string", format="string", example="Some notes"),
 * @OA\Property(property="bank_payment", type="number", format="double", example=100.00),
 * @OA\Property(property="cash_payment", type="number", format="double", example=50.50),
 * @OA\Property(property="delivery_orders", type="integer", format="int32", example=8),
 *  *  * @OA\Property(property="delivery_orders_amount", type="integer", format="int32", example=8),
 * @OA\Property(property="restaurant_id", type="integer", format="int64", example=1),


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


    public function updateDailyOrderPartnerSale (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  tap(DailyOrderPartnerSale::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Get(
     *      path="/order/daily-order-partner-sale/get-all/{restaurant_id}",
     *      operationId="getDailyOrderPartnerSale",
     *      tags={"daily-order-partner-sale"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      *        *  @OA\Parameter(
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

    public function getDailyOrderPartnerSale ($restaurant_id,Request $request) {
        $dashboard_widget =  DailyOrderPartnerSale::
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
     *      path="/order/daily-order-partner-sale/get/{id}",
     *      operationId="getDailyOrderPartnerSaleById",
     *      tags={"daily-order-partner-sale"},
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

    public function getDailyOrderPartnerSaleById ($id, Request $request) {
        $dashboard_widget =  DailyOrderPartnerSale::where([
            "id" => $id
        ])->first();
        return response($dashboard_widget, 200);
    }

/**
        *
     * @OA\Delete(
     *      path="/order/daily-order-partner-sale/delete/{id}",
     *      operationId="deleteDailyOrderPartnerSaleById",
     *      tags={"daily-order-partner-sale"},
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

    public function deleteDailyOrderPartnerSaleById ($id, Request $request) {
        DailyOrderPartnerSale::where([
            "id" => $id
        ])->delete();
        return response(["success"=>true], 200);
    }
}

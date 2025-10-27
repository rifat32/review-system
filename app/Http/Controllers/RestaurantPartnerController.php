<?php

namespace App\Http\Controllers;

use App\Models\RestaurantPartner;
use Illuminate\Http\Request;

class RestaurantPartnerController extends Controller
{


    /**
        *
     * @OA\Post(
     *      path="/superadmin/restaurant-partner/create",
     *      operationId="createRestaurantPartner",
     *      tags={"superadmin-restaurant-partner"},
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
     *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="12345678"),
     *            @OA\Property(property="webpage_link", type="string", format="string",example="admin"),
     *             @OA\Property(property="is_active", type="string", format="string",example="1")

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


     public function createRestaurantPartner (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  RestaurantPartner::create($body);
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Put(
     *      path="/superadmin/restaurant-partner/update",
     *      operationId="updateRestaurantPartner",
     *      tags={"superadmin-restaurant-partner"},
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
    *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="12345678"),
     *            @OA\Property(property="webpage_link", type="string", format="string",example="admin"),
     *  *            @OA\Property(property="is_active", type="string", format="string",example="1")
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


    public function updateRestaurantPartner (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  tap(RestaurantPartner::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($dashboard_widget, 200);
    }


     /**
        *
     * @OA\Get(
     *      path="/superadmin/restaurant-partner/get",
     *      operationId="getRestaurantPartnerSuperAdmin",
     *      tags={"superadmin-restaurant-partner"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
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

    public function getRestaurantPartnerSuperAdmin (Request $request) {
        $dashboard_widget =  RestaurantPartner::get();
        return response()->json($dashboard_widget, 200);
    }

    /**
        *
     * @OA\Get(
     *      path="/restaurant-partner/get",
     *      operationId="getRestaurantPartner",
     *      tags={"restaurant-partner"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
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

     public function getRestaurantPartner (Request $request) {
        $dashboard_widget =  RestaurantPartner::where([
            "is_active" => 1
        ])->get();
        return response()->json($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Get(
     *      path="/superadmin/restaurant-partner/get/{id}",
     *      operationId="getRestaurantPartnerById",
     *      tags={"superadmin-restaurant-partner"},
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

    public function getRestaurantPartnerById ($id, Request $request) {
        $dashboard_widget =  RestaurantPartner::where([
            "id" => $id
        ])->first();
        return response($dashboard_widget, 200);
    }

/**
        *
     * @OA\Delete(
     *      path="/superadmin/restaurant-partner/delete/{id}",
     *      operationId="deleteRestaurantPartnerById",
     *      tags={"superadmin-restaurant-partner"},
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

    public function deleteRestaurantPartnerById ($id, Request $request) {
        RestaurantPartner::where([
            "id" => $id
        ])->delete();
        return response(["success"=>true], 200);
    }


}

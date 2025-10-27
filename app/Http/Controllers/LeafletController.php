<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Models\Leaflet;
use App\Models\Restaurant;
use Exception;
use Illuminate\Http\Request;

class LeafletController extends Controller
{
        /**
        *
     * @OA\Post(
     *      path="/leaflet/create",
     *      operationId="createLeaflet",
     *      tags={"leaflet"},
     *      security={
     *           {"bearerAuth": {}}
     *       },

     *      summary="This method is to store leaflet",
     *      description="This method is to store leaflet",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={},
     * *             @OA\Property(property="title", type="string", format="string",example="1"),
     *             @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *            @OA\Property(property="thumbnail", type="string", format="string",example="1"),
     * *            @OA\Property(property="leaflet_data", type="string", format="string",example="1"),
     * *            @OA\Property(property="type", type="string", format="string",example="1"),


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
    public function createLeaflet (Request $request) {
        $body = $request->toArray();
        if (!$request->user()->hasRole("superadmin")) {
            $restaurantFound = Restaurant::where([
                "id" => $body["restaurant_id"]
            ])
            ->first();
            if(!$restaurantFound) {
                return response()->json([
                    "message" => "business not found"
                ]);
            }
        }

        $leaflet =  Leaflet::create($body);
        return response($leaflet, 200);
    }

     /**
        *
     * @OA\Put(
     *      path="/leaflet/update",
     *      operationId="updateLeaflet",
     *      tags={"leaflet"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update leaflet",
     *      description="This method is to update leaflet",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={},
       *
       * *             @OA\Property(property="id", type="string", format="string",example="1"),
       *    * *             @OA\Property(property="title", type="string", format="string",example="1"),
     *             @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *            @OA\Property(property="thumbnail", type="string", format="string",example="1"),
     * *            @OA\Property(property="leaflet_data", type="string", format="string",example="1"),
     * *            @OA\Property(property="type", type="string", format="string",example="1"),


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
    public function updateLeaflet (Request $request) {
        $body = $request->toArray();
        if (!$request->user()->hasRole("superadmin")) {
            $restaurantFound = Restaurant::where([
                "id" => $body["restaurant_id"]
            ])
            ->first();
            if(!$restaurantFound) {
                return response()->json([
                    "message" => "business not found"
                ]);
            }
        }
        $leaflet =  tap(Leaflet::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($leaflet, 200);
    }


  /**
        *
     * @OA\Get(
       *      path="/leaflet/get",
     *      operationId="getLeaflet",
     *      tags={"leaflet"},

     *      *  @OA\Parameter(
* name="restaurant_id",
* in="query",
* description="restaurant_id",
* required=true,
* example="1"
* ),
    *      *  @OA\Parameter(
* name="type",
* in="query",
* description="type",
* required=true,
* example="1"
* ),
     *      summary="This method is to get leaflet ",
     *      description="This method is to get leaflet ",
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

    public function getLeaflet (Request $request) {

        $leafletsQuery = new Leaflet();

        if(!empty($request->restaurant_id)) {
            $leafletsQuery = $leafletsQuery->where([
                "restaurant_id"=>$request->restaurant_id
            ]);
        }
        if(!empty($request->type)) {
            $leafletsQuery = $leafletsQuery->where([
                "type"=>$request->type
            ]);
        }

        $leaflets =  $leafletsQuery->orderByDesc("id")->get();
        return response($leaflets, 200);
    }

      /**
        *
     * @OA\Get(
     *      path="/leaflet/get/{id}",
     *      operationId="getLeafletById",
     *      tags={"leaflet"},

     *  @OA\Parameter(
* name="id",
* in="path",
* description="id",
* required=true,
* example="1"
* ),
     *      summary="This method is to get leaflet  by id",
     *      description="This method is to get single leaflet  by id",
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

    public function getLeafletById ($id, Request $request) {
        $leaflet =  Leaflet::where([
            "id" => $id
        ])->first();
        return response($leaflet, 200);
    }


        /**
        *
     * @OA\Delete(
     *      path="/leaflet/{restaurant_id}/{id}",
     *      operationId="deleteLeafletById",
     *      tags={"leaflet"},
     *      security={
     *           {"bearerAuth": {}}
     *       },
     *      *  @OA\Parameter(
* name="restaurant_id",
* in="path",
* description="restaurant_id",
* required=true,
* example="1"
* ),
     *  @OA\Parameter(
* name="id",
* in="path",
* description="id",
* required=true,
* example="1"
* ),
     *      summary="This method is to delete leaflet  by id",
     *      description="This method is to delete leaflet  by id",
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

    public function deleteLeafletById ($restaurant_id,$id, Request $request) {
        if (!$request->user()->hasRole("superadmin")) {
            $restaurantFound = Restaurant::where([
                "id" => $restaurant_id
            ])
            ->first();
            if(!$restaurantFound) {
                return response()->json([
                    "message" => "business not found"
                ]);
            }
        }
        $leaflet =  Leaflet::where([
            "id" => $id
        ])->delete();
        return response(["ok" => true], 200);
    }











         /**
        *
     * @OA\Post(
     *      path="/v1.0/leaflet-image",
     *      operationId="createLeafletImage",
     *      tags={"leaflet"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store leaflet image ",
     *      description="This method is to store leaflet image",

   *  @OA\RequestBody(
        *   * @OA\MediaType(
*     mediaType="multipart/form-data",
*     @OA\Schema(
*         required={"image"},
*         @OA\Property(
*             description="image to upload",
*             property="image",
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

    public function createLeafletImage(ImageUploadRequest $request)
    {
        try{


            $insertableData = $request->validated();


            $location =  "leaflet_image";

            $new_file_name = time() . '_' . $insertableData["image"]->getClientOriginalName();

            $insertableData["image"]->move(public_path($location), $new_file_name);


                return response()->json([
                    "image" => ("/".$location."/".$new_file_name),

                ], 200);


        } catch(Exception $e){
            error_log($e->getMessage());
        return response()->json(["message" => $e->getMessage()],500);
        }
    }
}

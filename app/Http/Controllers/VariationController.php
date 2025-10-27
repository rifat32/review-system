<?php

namespace App\Http\Controllers;

use App\Models\DishVariation;
use App\Models\Variation;
use App\Models\VariationType;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    // ##################################################
    // This method is to store variation type
    // ##################################################

    /**
     *
     * @OA\Post(
     *      path="/variation/variation_type",
     *      operationId="storeVariationType",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store variation type",
     *      description="This method is to store variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description","restaurant_id"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="test"),
     *            @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *             @OA\Property(property="order_number", type="string", format="string",example="1"),
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


    public function storeVariationType(Request $request)
    {


        $variation_type =  VariationType::create($request->toArray());


        return response($variation_type, 200);
    }

    /**
     *
     * @OA\Delete(
     *      path="/variation/variation_type/{id}",
     *      operationId="deleteVariationType",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *
     *      summary="This method is to store variation type",
     *      description="This method is to delete variation type",

     *        @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
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


    public function deleteVariationType($id, Request $request)
    {


        VariationType::where([
            "id" => $id,
        ])
            ->delete();

        return response(["message" => "ok"], 200);
    }

    /**
     *
     * @OA\Delete(
     *      path="/variation/variation/{id}",
     *      operationId="deleteVariation",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *
     *      summary="This method is to delete variation ",
     *      description="This method is to delete variation ",

     *        @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
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


    public function deleteVariation($id, Request $request)
    {


        Variation::where([
            "id" => $id,
        ])
            ->delete();

        return response(["message" => "ok"], 200);
    }
    // ##################################################
    // This method is to store multiple variation type
    // ##################################################



    /**
     *
     * @OA\Post(
     *      path="/variation/variation_type/multiple/{restaurantId}",
     *      operationId="storeMultipleVariationType",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to store multiple variation type",
     *      description="This method is to store multiple variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"VarationType"},
     *  @OA\Property(property="VarationType", type="string", format="array",example={

     *  {	"name":"hggggg","description":"fffffffffff","order_number":1},
     *  {	"name":"hggggg","description":"fffffffffff","order_number":1},
     * }

     *
     * ),

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


    public function storeMultipleVariationType($restaurantId, Request $request)
    {
        $variation_types = $request->VarationType;
        $duplicate_indexes_array = [];
        $uniqueVariations = collect($request->VarationType)->unique('name');
        $uniqueVariations->values()->all();
        foreach ($uniqueVariations as $index => $variation_type) {
            $typeFound =    VariationType::where(["restaurant_id" => $restaurantId,
            "name" => $variation_type["name"],
            "description" => $variation_type["description"],
            ])

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

            $arr = [];
            foreach ($uniqueVariations as $index => $variation_type) {

                $variation_type["restaurant_id"] = $restaurantId;
                $createdVariationType =  VariationType::create($variation_type);

                array_push($arr, $createdVariationType);
            }
        }

        return response($arr, 201);
    }
    // ##################################################
    // This method is to update multiple variation type
    // ##################################################



    /**
     *
     * @OA\Patch(
     *      path="/variation/variation_type/multiple",
     *      operationId="updateMultipleVariationType",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update multiple variation type",
     *      description="This method is to update multiple variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"VarationType"},
     *  @OA\Property(property="VarationType", type="string", format="array",example={

     *  {"varation_type_id":"1","name":"Multiple","description":"Multiple","order_number":1},
     *  {"varation_type_id":"2","name":"Multiple","description":"Multiple","order_number":1},

     * }
     *
     * ),

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


    public function updateMultipleVariationType(Request $request)
    {


        $variation_types = $request->VarationType;
        $duplicate_indexes_array = [];

        $uniqueVariations = collect($request->VarationType)->unique('name');
        $uniqueVariations->values()->all();
        foreach ($uniqueVariations as $index => $variation_type) {

            $type =    VariationType::where(["id" => $variation_type["varation_type_id"]])
                ->first();
            if (!$type) {
                return response()->json(
                    [
                        "message" => ("tag not found at position" . $index)
                    ],
                    422
                );
            }
            $typeFound =    VariationType::where([
                "restaurant_id" => $type->restaurant_id,
                "name" => $variation_type["name"]
            ])
                ->whereNotIn("id", [$type->id])

                ->first();

            if ($typeFound) {
                array_push($duplicate_indexes_array, [$index, $typeFound]);
            }
        }

        if (count($duplicate_indexes_array)) {


            return response([
                "message" => "duplicate data",
                "duplicate_indexes_array" => $duplicate_indexes_array
            ], 409);
        } else {
            $arr = [];
            foreach ($uniqueVariations as $index => $variation_type) {

                $createdVariationType =    tap(VariationType::where(["id" => $variation_type["varation_type_id"]]))->update(
                    collect($variation_type)->only(['name', 'description',"order_number"])->all()
                )
                    // ->with("somthing")
                    ->first();
            }
            array_push($arr, $createdVariationType);
        }






        return response($arr, 200);
    }

    /**
     *
     * @OA\Patch(
     *      path="/variation/variation/multiple",
     *      operationId="updateMultipleVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update multiple variation ",
     *      description="This method is to update multiple variation ",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"variations"},
     *     @OA\Property(property="restaurant_id", type="number", format="number",example="1"),
     *  @OA\Property(property="variations", type="string", format="array",example={

     *  {"id":"1","name":"Multiple","description":"Multiple","price":"10"},
     *  {"id":"1","name":"Multiple","description":"Multiple","price":"10"},

     * }
     *
     * ),

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


    public function updateMultipleVariation(Request $request)
    {



        $duplicate_indexes_array = [];

        $uniqueVariations = collect($request->variations);
        // ->unique('name');
        $uniqueVariations->values()->all();


        foreach ($uniqueVariations as $index => $variation_type) {

            $variation =   Variation::where([
                "id" => $variation_type["id"]
            ])
                ->first();
            if (!$variation) {
                return response()->json([], 200);
            }

            $typeFound =    Variation::leftJoin('variation_types', 'variation_types.id', '=', 'variations.type_id')
                ->where(
                    [
                        "variation_types.restaurant_id" => $request->restaurant_id,
                        "variations.name" => $variation_type["name"]
                    ]
                )
                ->whereNotIn("variations.id", [$variation->id])


                ->first();

            if ($typeFound) {

                array_push($duplicate_indexes_array, [$index, $typeFound]);
            }
        }

        if (count($duplicate_indexes_array)) {

            return response([
                "message" => "duplicate data",
                "duplicate_indexes_array" => $duplicate_indexes_array
            ], 409);
        } else {
            $arr = [];
            foreach ($uniqueVariations as $index => $variation_type) {

                $createdVariation =    tap(Variation::where(["id" => $variation_type["id"]]))->update(
                    collect($variation_type)->only(['name', 'description', 'price'])->all()
                )
                    // ->with("somthing")
                    ->first();
                array_push($arr, $createdVariation);
            }
        }

        return response($arr, 201);
    }


    // ##################################################
    // This method is to update variation type
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/variation/variationtype",
     *      operationId="updateVariationType",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update variation type",
     *      description="This method is to update variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description","VTypeId"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="test"),
     *              @OA\Property(property="VTypeId", type="string", format="string",example="1"),
     * *              @OA\Property(property="order_number", type="string", format="string",example="1"),
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



    public function updateVariationType(Request $request)
    {

        $createdVariationType =    tap(VariationType::where(["id" => $request->VTypeId]))->update(
            $request->only(
                'name',
                'description',
                "order_number"
            )
        )
            // ->with("somthing")

            ->first();


        return response($createdVariationType, 200);
    }
    // ##################################################
    // This method is to store variation
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/variation",
     *      operationId="storeVariation",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary=" This method is to store variation",
     *      description=" This method is to store variation",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description","type_id"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="test"),
     *              @OA\Property(property="type_id", type="number", format="number",example="1"),
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
    public function storeVariation(Request $request)
    {

        $variation =  Variation::create($request->toArray());

        return response($variation, 200);
    }
    // ##################################################
    // This method is to store multiple variation
    // ##################################################
    /**
     *
     * @OA\Post(
     *      path="/variation/multiple/varations",
     *      operationId="storeMultipleVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store multiple variation ",
     *      description="This method is to store multiple variation ",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"varation","restaurant_id"},
     *   *              @OA\Property(property="restaurant_id", type="string", format="string",example="1"),
     *  @OA\Property(property="varation", type="string", format="array",example={

     *  {"name":"ssssss","description":"Multiple","type_id":1,"price":90},
     *  {"name":"ssssss","description":"Multiple","type_id":1,"price":90},

     * }
     *
     * ),

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


    public function storeMultipleVariation(Request $request)
    {



        $duplicate_indexes_array = [];

        $uniqueVariations = collect($request->varation)->unique('name');
        $uniqueVariations->values()->all();
        foreach ($uniqueVariations as $index => $variation_type) {


            $typeFound =   Variation::leftJoin('variation_types', 'variation_types.id', '=', 'variations.type_id')
                ->where(
                    [
                        "variation_types.restaurant_id" => $request->restaurant_id,
                        "variations.name" => $variation_type["name"]
                    ]
                )
                ->whereIn(
                 "variation_types.id", [$variation_type["type_id"]]
                )

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

            $arr = [];

            foreach ($uniqueVariations as $index => $variation_type) {

                $variation =  Variation::create($variation_type);

                array_push($arr, $variation);
            }
        }

        return response($arr, 201);
    }

    // ##################################################
    // This method is to update variation
    // ##################################################

    /**
     *
     * @OA\Patch(
     *      path="/variation",
     *      operationId="updateVariation",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update variation",
     *      description="This method is to update variation",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description","Vid"},
     *
     *             @OA\Property(property="name", type="string", format="string",example="test"),
     *            @OA\Property(property="description", type="string", format="string",example="test"),
     *              @OA\Property(property="Vid", type="number", format="number",example="1"),
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



    public function updateVariation(Request $request)
    {
        $updatedVariation =    tap(Variation::where(["id" => $request->Vid]))->update(
            $request->only(
                'name',
                'description'
            )
        )
            // ->with("somthing")

            ->first();
        return response($updatedVariation, 200);
    }
    // ##################################################
    // This method is to store dish variation
    // ##################################################


    /**
     *
     * @OA\Post(
     *      path="/variation/dish_variation",
     *      operationId="storeDishVariation",
     *      tags={"variation"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store dish variation",
     *      description="This method is to store dish variation",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"minimum_variation_required","no_of_varation_allowed","type_id","dish_id"},
     *               @OA\Property(property="minimum_variation_required", type="number", format="number",example="1"),
     *             @OA\Property(property="no_of_varation_allowed", type="number", format="number",example="1"),
     *            @OA\Property(property="type_id", type="number", format="number",example="test"),
     *              @OA\Property(property="dish_id", type="number", format="number",example="1"),
     *     *              @OA\Property(property="order_number", type="number", format="number",example="1"),
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
    public function  storeDishVariation(Request $request)
    {

        $dishVariation =  DishVariation::create($request->toArray());

        return response($dishVariation, 200);
    }
    // ##################################################
    // This method is to store multiple dish variation
    // ##################################################

    /**
     *
     * @OA\Post(
     *      path="/variation/multiple/dish_variation/{dishId}",
     *      operationId="storeMultipleDishVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="dishId",
     * in="path",
     * description="dishId",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to store multiple dish variation",
     *      description="This method is to store multiple dish variation",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"varation"},
     *  @OA\Property(property="varation", type="string", format="array",example={

     *  {"minimum_variation_required","no_of_varation_allowed":5,"type_id":1,"order_number":1},
     *  {"minimum_variation_required","no_of_varation_allowed":5,"type_id":2,"order_number":1},

     * }
     *
     * ),

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


    public function storeMultipleDishVariation($dishId, Request $request)
    {
        $variations = $request->varation;
        $variation_array = [];
        foreach ($variations as $variation) {
            $variation["dish_id"] = $dishId;

            if($variation["is_checked"]) {
                $createdVariationType =  DishVariation::create($variation);
                array_push($variation_array, $createdVariationType);
            }


        }

        return response($variation_array, 201);
    }
    // ##################################################
    // This method is to get all dish variation
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/variation/dish_variation/{dishId}",
     *      operationId="getAllDishVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="dishId",
     * in="path",
     * description="dishId",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to get all dish variation",
     *      description="This method is to get all dish variation",
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
    public function getAllDishVariation($dishId, Request $request)
    {
        $dishVariations = DishVariation::with("variation_type", "variation_type.variation", "dish")
        ->where([
            "dish_id" => $dishId
        ])
        ->orderBy("dish_variations.order_number")
        ->get();


        return response($dishVariations, 201);
    }

    // ##################################################
    // This method is to update  dish variation
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/variation/dish_variation",
     *      operationId="updateDishVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update multiple variation type",
     *      description="This method is to update multiple variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"no_of_varation_allowed","minimum_variation_required","type_id","dish_id"},
     *             @OA\Property(property="minimum_variation_required", type="number", format="number",example="test"),
     *             @OA\Property(property="no_of_varation_allowed", type="number", format="number",example="test"),
     *             @OA\Property(property="type_id", type="number", format="number",example="test"),
     *             @OA\Property(property="dish_id", type="number", format="number",example="1"),
     *             @OA\Property(property="order_number", type="number", format="number",example="1"),
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

    public function updateDishVariation(Request $request)
    {
        $updatedDishVariation =    tap(DishVariation::where(["dish_id" => $request->dish_id]))->update(
            $request->only(
                'minimum_variation_required',
                'no_of_varation_allowed',
                'type_id',
                "order_number"
            )
        )
            // ->with("somthing")

            ->first();
        return response($updatedDishVariation, 200);
    }
    // ##################################################
    // This method is to update  dish variation
    // ##################################################
    /**
     *
     * @OA\Patch(
     *      path="/variation/dish_variation/multiple/{dishId}",
     *      operationId="updateMultipleDishVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *   *              @OA\Parameter(
     *         name="dishId",
     *         in="path",
     *         description="dishId",
     *         required=true,
     * example="1"
     *      ),
     *      summary="This method is to update multiple variation type",
     *      description="This method is to update multiple variation type",
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"DishVariations"},

     *  @OA\Property(property="DishVariations", type="string", format="array",example={	{"minimum_variation_required","no_of_varation_allowed":"10","type_id":10,"dish_id":1,"order_number":1}

     * }
     *
     * ),


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

    public function updateMultipleDishVariation($dishId, Request $request)
    {



        DishVariation::where(["dish_id" => $dishId])->delete();
        foreach ($request->DishVariations as $variation) {
            if($variation["is_checked"]){
                DishVariation::create([

                    'dish_id' =>  $dishId,
                    'no_of_varation_allowed' => $variation["no_of_varation_allowed"],
                    'minimum_variation_required' => $variation['minimum_variation_required'],
                    'type_id' => $variation['type_id'],
                    "order_number" => $variation["order_number"]


                ]);
            }

        }
        return response(["message" => "data inserted"], 201);
    }

    // ##################################################
    // This method is to get all  variation  with dish
    // ##################################################

    /**
     *
     * @OA\Get(
     *      path="/variation/{restaurantId}",
     *      operationId="getAllVariationWithDish",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),

     *      summary="This method is to get all  variation  with dish",
     *      description="This method is to get all  variation  with dish",
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
    public function getAllVariationWithDish($restaurantId, Request $request)
    {
        $dishAndVariations = VariationType::with("variation", "dish_variation")->where([
            "restaurant_id" => $restaurantId
        ])
        ->orderBy("variation_types.order_number")
            ->get();


        return response($dishAndVariations, 201);
    }
    /**
     *
     * @OA\Get(
     *      path="/variation-type/{id}",
     *      operationId="getSingleVariationType",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="id",
     * in="path",
     * description="id",
     * required=true,
     * example="1"
     * ),

     *      summary="This method is to get single variation type",
     *      description="This method is to get single variation type",
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
    public function getSingleVariationType($id, Request $request)
    {
        $variationType = VariationType::with("variation", "dish_variation")->where([
            "id" => $id
        ])


            ->first();


        return response($variationType, 201);
    }


    /**
     *
     * @OA\Get(
     *      path="/variation2/{restaurantId}',",
     *      operationId="getAllVariationTypeWithVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),

     *      summary="This method is to get all  variation  with dish2",
     *      description="This method is to get all  variation  with dish2",
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
    public function getAllVariationTypeWithVariation($restaurantId, Request $request)
    {


        $dishAndVariations = VariationType::withCount('variation')->where([
            "restaurant_id" => $restaurantId
        ])
        ->orderBy("variation_types.order_number")

        ->get()->map(function($variationType)  {
            $variationType->is_checked = 0; // Initialize is_checked flag

            $variationType->no_of_varation_allowed = $variationType->variation_count;
            $variationType->minimum_variation_required =  $variationType->variation_count;
            $variationType->dish_id = NULL;




            return $variationType;
        });


        return response($dishAndVariations, 201);
    }
    /**
     *
     * @OA\Get(
     *      path="/variation2/{restaurantId}/{dishId}",
     *      operationId="getAllVariationTypeWithVariationByDishId",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="restaurantId",
     * in="path",
     * description="restaurantId",
     * required=true,
     * example="1"
     * ),
     *    *  @OA\Parameter(
     * name="dishId",
     * in="path",
     * description="dishId",
     * required=true,
     * example="1"
     * ),

     *      summary="This method is to get all  variation  with dish2",
     *      description="This method is to get all  variation  with dish2",
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
    public function getAllVariationTypeWithVariationByDishId($restaurantId,$dishId, Request $request)
    {


        $dishVariations = DishVariation::where([
            "dish_id" => $dishId
        ])->get();

        $variationTypes = VariationType::withCount('variation')->where([
            "restaurant_id" => $restaurantId
        ])->get()->map(function($variationType) use ($dishVariations) {
            $variationType->is_checked = 0; // Initialize is_checked flag

            $variationType->no_of_varation_allowed = $variationType->variation_count;
            $variationType->minimum_variation_required =  $variationType->variation_count;
            $variationType->dish_id = NULL;


            // Assign order_number and update is_checked flag based on type_id
            foreach ($dishVariations as $item) {
                if ($item['type_id'] == $variationType->id) {
                    $variationType->order_number = $item['order_number']; // Assign order_number
                    $variationType->is_checked = 1; // Update is_checked flag
                    $variationType->no_of_varation_allowed = $item['no_of_varation_allowed'];
                    $variationType->minimum_variation_required = $item['minimum_variation_required'];
                    $variationType->dish_id = $item['dish_id'];

                    break; // Break the loop after assigning the order_number
                }
            }

            return $variationType;
        });

// Manually sort the collection by is_checked and then by order_number attribute using usort()
$sortedVariationTypes = $variationTypes->toArray();
usort($sortedVariationTypes, function ($a, $b) {
    // First, compare by is_checked
    $is_checkedComparison = $b['is_checked'] - $a['is_checked'];

    // If is_checked is the same, then compare by order_number
    if ($is_checkedComparison == 0) {
        return $a['order_number'] - $b['order_number'];
    }

    return $is_checkedComparison;
});

        return response($sortedVariationTypes, 201);

    }
    // ##################################################
    // This method is to get all  variation   by type id
    // ##################################################
    /**
     *
     * @OA\Get(
     *      path="/variation/type/count/{typeId}",
     *      operationId="getAllVariationByType_Id",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="typeId",
     * in="path",
     * description="typeId",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to get all  variation   by type id",
     *      description="This method is to get all  variation   by type id",
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
    public function getAllVariationByType_Id($typeId, Request $request)
    {
        $dishAndVariations = Variation::with("variation_type", "variation_type.dish_variation")->where([
            "type_id" => $typeId
        ])
            ->get();

        return response($dishAndVariations, 201);
    }


    /**
     *
     * @OA\Get(
     *      path="/variation/by-restaurant-id/{restaurant_id}",
     *      operationId="getAllVariationByRestaurantId",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="restaurant_id",
     * in="path",
     * description="restaurant_id",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to get all  variation   by type restaurant id",
     *      description="This method is to get all  variation   by restaurant id",
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
    public function getAllVariationByRestaurantId($restaurant_id, Request $request)
    {
        $dishAndVariations = Variation::leftJoin('variation_types', 'variation_types.id', '=', 'variations.type_id')
            ->with("variation_type", "variation_type.dish_variation")->where([
                "variation_types.restaurant_id" => $restaurant_id
            ])
            ->get();




        return response($dishAndVariations, 201);
    }





    // ##################################################
    // This method is to delete dish variation
    // ##################################################

    /**
     *
     * @OA\Delete(
     *      path="/variation/unlink/{typeId}/{dishId}",
     *      operationId="deleteDishVariation",
     *      tags={"variation"},
     *   *       security={
     *           {"bearerAuth": {}}
     *       },
     *  @OA\Parameter(
     * name="typeId",
     * in="path",
     * description="typeId",
     * required=true,
     * example="1"
     * ),
     *  @OA\Parameter(
     * name="dishId",
     * in="path",
     * description="dishId",
     * required=true,
     * example="1"
     * ),
     *      summary="This method is to delete dish variation",
     *      description="This method is to delete dish variation",
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
    public function deleteDishVariation($typeId, $dishId, Request $request)
    {
        DishVariation::where([
            "type_id" => $typeId,
            'dish_id' => $dishId,
        ])
            ->delete();



        return response(["message" => "ok"], 200);
    }
}

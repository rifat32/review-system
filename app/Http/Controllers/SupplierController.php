<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Utils\ErrorUtil;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    use ErrorUtil;

    /**
     *
     * @OA\Post(
     *      path="/v1.0/suppliers",
     *      operationId="createSupplier",
     *      tags={"supplier_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store supplier",
     *      description="This method is to store supplier",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={},
     *    @OA\Property(property="name", type="string", format="string",example=""),
     *    @OA\Property(property="contact_person", type="string", format="string",example=""),
     *    @OA\Property(property="phone", type="string", format="string",example=""),
     *    @OA\Property(property="email", type="string", format="string",example=""),
     *    @OA\Property(property="address", type="string", format="string",example=""),
     *    @OA\Property(property="note", type="string", format="string",example=""),
     *    @OA\Property(property="is_active", type="boolean", format="boolean",example="true"),
     *    @OA\Property(property="restaurant_id", type="boolean", format="boolean",example="true")
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

    public function createSupplier(SupplierCreateRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                $insertableData = $request->validated();

                $supplier =  Supplier::create($insertableData);

                return response($supplier, 201);
            });
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ], 404);
        }
    }

    /**
     *
     * @OA\Put(
     *      path="/v1.0/suppliers",
     *      operationId="updateSupplier",
     *      tags={"supplier_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update supplier",
     *      description="This method is to update supplier",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={},
     *  *    @OA\Property(property="id", type="number", format="number",example="1"),
     *    @OA\Property(property="name", type="string", format="string",example=""),
     *    @OA\Property(property="contact_person", type="string", format="string",example=""),
     *    @OA\Property(property="phone", type="string", format="string",example=""),
     *    @OA\Property(property="email", type="string", format="string",example=""),
     *    @OA\Property(property="address", type="string", format="string",example=""),
     *    @OA\Property(property="note", type="string", format="string",example=""),
     *    @OA\Property(property="is_active", type="boolean", format="boolean",example="true"),
     *    @OA\Property(property="restaurant_id", type="boolean", format="boolean",example="true")
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

    public function updateSupplier(SupplierUpdateRequest $request)
    {
        try {

            return  DB::transaction(function () use ($request) {

                $updatableData = $request->validated();

                $supplier  =  Supplier::where(["id" => $updatableData["id"]])
                    ->first();

                if (!$supplier) {
                    $this->storeError(
                        "no data found",
                        404,
                        "front end error",
                        "front end error"
                    );
                    return response()->json([
                        "message" => "no supplier found"
                    ], 404);
                }

                $supplier->fill($updatableData);
                $supplier->save();

                return response($supplier, 201);
            });
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ], 404);
        }
    }
    /**
     *
     * @OA\Get(
     *      path="/v1.0/suppliers/{restaurant_id}",
     *      operationId="getAllSuppliers",
     *      tags={"supplier_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },

     *              @OA\Parameter(
     *         name="restaurant_id",
     *         in="path",
     *         description="restaurant_id",
     *         required=true,
     *  example="6"
     *      ),

     *      * *  @OA\Parameter(
     * name="start_date",
     * in="query",
     * description="start_date",
     * required=true,
     * example="2019-06-29"
     * ),
     * *  @OA\Parameter(
     * name="end_date",
     * in="query",
     * description="end_date",
     * required=true,
     * example="2019-06-29"
     * ),
     * *  @OA\Parameter(
     * name="search_key",
     * in="query",
     * description="search_key",
     * required=true,
     * example="search_key"
     * ),
     *      summary="This method is to get suppliers ",
     *      description="This method is to get suppliers",
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

    public function getAllSuppliers($restaurant_id, Request $request)
    {
        try {

            $supplierQuery = Supplier::where([
                 "restaurant_id" => $restaurant_id
             ]);

            if (!empty($request->search_key)) {
                $term = $request->search_key;
                $supplierQuery->where(function ($query) use ($term) {
                    $query->where("name", "like", "%" . $term . "%")
                        ->orWhere("contact_person", "like", "%" . $term . "%")
                        ->orWhere("phone", "like", "%" . $term . "%")
                        ->orWhere("email", "like", "%" . $term . "%")
                        ->orWhere("address", "like", "%" . $term . "%")
                        ->orWhere("note", "like", "%" . $term . "%");
                });
            }

            if (!is_null($request->is_active)) {
                $supplierQuery->where("is_active", $request->is_active);
            }

            if (!empty($request->start_date)) {
                $supplierQuery->where('created_at', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $supplierQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
            }

            $suppliers = $supplierQuery->orderByDesc("id")->get();

            return response()->json($suppliers, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ], 404);
        }
    }
    /**
     *
     * @OA\Get(
     *      path="/v1.0/suppliers/{restaurant_id}/{perPage}",
     *      operationId="getSuppliers",
     *      tags={"supplier_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },

     *              @OA\Parameter(
     *         name="restaurant_id",
     *         in="path",
     *         description="restaurant_id",
     *         required=true,
     *  example="6"
     *      ),
     *              @OA\Parameter(
     *         name="perPage",
     *         in="path",
     *         description="perPage",
     *         required=true,
     *  example="6"
     *      ),
     *      * *  @OA\Parameter(
     * name="start_date",
     * in="query",
     * description="start_date",
     * required=true,
     * example="2019-06-29"
     * ),
     * *  @OA\Parameter(
     * name="end_date",
     * in="query",
     * description="end_date",
     * required=true,
     * example="2019-06-29"
     * ),
     * *  @OA\Parameter(
     * name="search_key",
     * in="query",
     * description="search_key",
     * required=true,
     * example="search_key"
     * ),
     * *  @OA\Parameter(
     * name="is_active",
     * in="query",
     * description="is_active",
     * required=true,
     * example="is_active"
     * ),
     *      summary="This method is to get suppliers ",
     *      description="This method is to get suppliers",
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

    public function getSuppliers($restaurant_id, $perPage, Request $request)
    {
        try {



            $supplierQuery =  Supplier::where([
                "restaurant_id" => $restaurant_id
            ])
            ->when(request()->filled("is_active"), function ($query) {
                    $query->where("suppliers.is_active", request()->input("is_active"));
                });
            if (!empty($request->search_key)) {
                $supplierQuery = $supplierQuery->where(function ($query) use ($request) {
                    $term = $request->search_key;
                     $query->where("suppliers.name", "like", "%" . $term . "%")
                        ->orWhere("suppliers.contact_person", "like", "%" . $term . "%")
                        ->orWhere("suppliers.phone", "like", "%" . $term . "%")
                        ->orWhere("suppliers.email", "like", "%" . $term . "%")
                        ->orWhere("suppliers.address", "like", "%" . $term . "%")
                        ->orWhere("suppliers.note", "like", "%" . $term . "%");
                });
            }

            if (!empty($request->start_date)) {
                $supplierQuery = $supplierQuery->where('suppliers.created_at', ">=", $request->start_date);
            }
            if (!empty($request->end_date)) {
                $supplierQuery = $supplierQuery->where('suppliers.created_at', "<=", ($request->end_date . ' 23:59:59'));
            }

            $suppliers = $supplierQuery->orderByDesc("suppliers.id")->paginate($perPage);

            return response()->json($suppliers, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ], 404);
        }
    }


    /**
     *
     *     @OA\Delete(
     *      path="/v1.0/suppliers/{id}",
     *      operationId="deleteSupplierById",
     *      tags={"supplier_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *              @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
     *  example="1"
     *      ),
     *      summary="This method is to delete supplier by id",
     *      description="This method is to delete supplier by id",
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

    public function deleteSupplierById($id, Request $request)
    {

        try {

            Supplier::where([
                "id" => $id
            ])
                ->delete();

            return response()->json(["ok" => true], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ], 404);
        }
    }
}

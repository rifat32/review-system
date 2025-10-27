<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseTypeCreateRequest;
use App\Http\Requests\ExpenseTypeUpdateRequest;
use App\Http\Utils\ErrorUtil;
use App\Models\ExpenseType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseTypeController extends Controller
{
    use ErrorUtil;

    /**
     *
     * @OA\Post(
     *      path="/v1.0/expense-types",
     *      operationId="createExpenseType",
     *      tags={"expense_type_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store expense type",
     *      description="This method is to store expense type",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","description","is_active"},
     *    @OA\Property(property="name", type="string", format="string",example="car"),
     *    @OA\Property(property="description", type="string", format="string",example="car"),
     *    @OA\Property(property="is_active", type="boolean", format="boolean",example="true"),
     *    @OA\Property(property="restaurant_id", type="boolean", format="boolean",example="true"),
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

    public function createExpenseType(ExpenseTypeCreateRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                $insertableData = $request->validated();

                $expense_type =  ExpenseType::create($insertableData);

                return response($expense_type, 201);


            });
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ],404);
        }
    }

    /**
     *
     * @OA\Put(
     *      path="/v1.0/expense-types",
     *      operationId="updateExpenseType",
     *      tags={"expense_type_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update expense type",
     *      description="This method is to update expense type",
     *
       *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id","name","description","is_active"},
     * *    @OA\Property(property="id", type="number", format="number",example="1"),
     *    @OA\Property(property="name", type="string", format="string",example="car"),
     *    @OA\Property(property="description", type="string", format="string",example="car"),
     *    @OA\Property(property="is_active", type="boolean", format="boolean",example="true"),
     *      *    @OA\Property(property="restaurant_id", type="boolean", format="boolean",example="true"),
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

    public function updateExpenseType(ExpenseTypeUpdateRequest $request)
    {
        try {

            return  DB::transaction(function () use ($request) {

                $updatableData = $request->validated();



                $expense_type  =  tap(ExpenseType::where(["id" => $updatableData["id"]]))->update(
                    collect($updatableData)->only([
        "name",
        "description",
        "is_active",
                    ])->toArray()
                )
                    // ->with("somthing")

                    ->first();
                    if(!$expense_type) {
                        $this->storeError(
                            "no data found"
                            ,
                            404,
                            "front end error",
                            "front end error"
                           );
                        return response()->json([
                            "message" => "no expense type found"
                            ],404);

                }

                return response($expense_type, 201);
            });
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ],404);
        }
    }
        /**
     *
     * @OA\Get(
     *      path="/v1.0/expense-types/{restaurant_id}",
     *      operationId="getAllExpenseTypes",
     *      tags={"expense_type_management"},
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
     *      summary="This method is to get expense types ",
     *      description="This method is to get expense types",
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

     public function getAllExpenseTypes($restaurant_id, Request $request)
     {
         try {


             $expenseTypeQuery =  ExpenseType::where([
                 "restaurant_id" => $restaurant_id
             ]);

             if (!empty($request->search_key)) {
                 $expenseTypeQuery = $expenseTypeQuery->where(function ($query) use ($request) {
                     $term = $request->search_key;
                     $query->where("name", "like", "%" . $term . "%");
                 });
             }

             if (!empty($request->start_date)) {
                 $expenseTypeQuery = $expenseTypeQuery->where('created_at', ">=", $request->start_date);
             }
             if (!empty($request->end_date)) {
                 $expenseTypeQuery = $expenseTypeQuery->where('created_at', "<=", ($request->end_date . ' 23:59:59'));
             }

             $expense_types = $expenseTypeQuery->orderByDesc("id")->get();


             return response()->json($expense_types, 200);
         } catch (Exception $e) {
             return response()->json([
                 "message" => "some thing went wrong",
                 "original_message" => $e->getMessage()
             ],404);
         }
     }
    /**
     *
     * @OA\Get(
     *      path="/v1.0/expense-types/{restaurant_id}/{perPage}",
     *      operationId="getExpenseTypes",
     *      tags={"expense_type_management"},
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
     *      summary="This method is to get expense types ",
     *      description="This method is to get expense types",
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

    public function getExpenseTypes($restaurant_id,$perPage, Request $request)
    {
        try {



            $expenseTypeQuery =  ExpenseType::where([
                "restaurant_id" => $restaurant_id
            ])
            ->when(request()->filled("is_active"), function($query) {
                $query->where("expense_types.is_active",request()->input("is_active"));
             });

            if (!empty($request->search_key)) {
                $expenseTypeQuery = $expenseTypeQuery->where(function ($query) use ($request) {
                    $term = $request->search_key;
                    $query->where("name", "like", "%" . $term . "%");
                });
            }

            if (!empty($request->start_date)) {
                $expenseTypeQuery = $expenseTypeQuery->where('created_at', ">=", $request->start_date);
            }
            if (!empty($request->end_date)) {
                $expenseTypeQuery = $expenseTypeQuery->where('created_at', "<=", ($request->end_date . ' 23:59:59'));
            }

            $expense_types = $expenseTypeQuery->orderByDesc("id")->paginate($perPage);
            return response()->json($expense_types, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ],404);
        }
    }

    /**
     *
     *     @OA\Delete(
     *      path="/v1.0/expense-types/{id}",
     *      operationId="deleteExpenseTypeById",
     *      tags={"expense_type_management"},
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
     *      summary="This method is to delete fuel station by id",
     *      description="This method is to delete fuel station by id",
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

    public function deleteExpenseTypeById($id, Request $request)
    {

        try {

            ExpenseType::where([
                "id" => $id
            ])
            ->delete();

            return response()->json(["ok" => true], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "some thing went wrong",
                "original_message" => $e->getMessage()
            ],404);
        }



    }
}

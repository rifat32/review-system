<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\InvoicePaymentCreateRequest;
use App\Http\Requests\InvoicePaymentUpdateRequest;

use App\Models\Expense;
use App\Models\Restaurant;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{

      /**
    *
 * @OA\Post(
 *      path="/v1.0/payments-invoice-file",
 *      operationId="createPaymentInvoiceFile",
 *      tags={"expense_management"},
 *       security={
 *           {"bearerAuth": {}}
 *       },
 *      summary="This method is to store reciept image",
 *      description="This method is to store reciept image",
 *
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

public function createPaymentInvoiceFile(FileUploadRequest $request)
{
    try{


        $insertableData = $request->validated();

        $location =  "invoices";

        $new_file_name = time() . '_' . str_replace(' ', '_', $insertableData["file"]->getClientOriginalName());

        $insertableData["file"]->move(public_path($location), $new_file_name);


        return response()->json(["file" => $new_file_name,"location" => $location,"full_location"=>("/".$location."/".$new_file_name)], 200);


    } catch(Exception $e){

        return response()->json([
            "message" => "some thing went wrong",
            "original_message" => $e->getMessage()
        ],404);
    }
}

/**
 *
 * @OA\Post(
 *      path="/v1.0/expenses",
 *      operationId="createInvoicePayment",
 *      tags={"expense_management"},
 *       security={
 *           {"bearerAuth": {}}
 *       },
 *      summary="This method is to store expenses",
 *      description="This method is to store expenses",
 *
 *  @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *            required={"name","description","logo"},
 *  *             @OA\Property(property="amount", type="number", format="number",example="10"),
  *             @OA\Property(property="payment_method", type="string", format="string",example="bkash"),
 *            @OA\Property(property="payment_date", type="string", format="string",example="2019-06-29"),
 *  *            @OA\Property(property="note", type="string", format="string",example="note"),
 *
 * *                  @OA\Property(property="restaurant_id", type="number", format="number",example="1"),
 *  *    *               @OA\Property(property="is_active", type="string", format="string",example="is_active"),
 *    *               @OA\Property(property="paid_by", type="string", format="string",example="paid_by"),
 *  *    *            @OA\Property(property="description", type="string", format="string",example="description"),
 *  *    *            @OA\Property(property="expense_type", type="string", format="string",example="expense type"),
 *  *  *    *            @OA\Property(property="supplier_id", type="string", format="string",example="supplier_id"),
 *  *    *            @OA\Property(property="reciepts", type="string", format="array",example={"a.jpg"}),

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

public function createInvoicePayment(InvoicePaymentCreateRequest $request)
{
    try {

        return DB::transaction(function () use ($request) {

            $insertableData = $request->validated();



            $restaurantFound = Restaurant::where([
                "id" => $insertableData["restaurant_id"]
            ])
            ->first();
            if (!$restaurantFound) {
    return response()->json([
        "message" => "No Business Found with this id"
    ],404);
            }

            $insertableData["created_by"] = $request->user()->id;

            $invoiceDateWithTime = Carbon::parse($insertableData["payment_date"]);
            $invoiceDateWithTime->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
            $insertableData["payment_date"] =  $invoiceDateWithTime;

            if(empty($insertableData["paid_by"])) {
                $insertableData["paid_by"] = $request->user()->first_Name . " " . $request->user()->last_Name;
            }




            $expense =  Expense::create($insertableData);

            if(!$expense) {
                throw new Exception("something went wrong");
            }
            $expense->generated_id = Str::random(4) . $expense->id . Str::random(4);

            $expense->shareable_link = env("FRONT_END_URL_DASHBOARD")."/share/receipt/". Str::random(4) . "-". $expense->generated_id ."-" . Str::random(4);

            $expense->save();


            return response($expense, 201);





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
 *      path="/v1.0/expenses",
 *      operationId="updateInvoicePayment",
 *      tags={"expense_management"},
 *       security={
 *           {"bearerAuth": {}}
 *       },
 *      summary="This method is to update expenses",
 *      description="This method is to update expenses",
 *
 *  @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *            required={"id","name","description","logo"},
 *     *             @OA\Property(property="id", type="number", format="number",example="1"),
 *  *             @OA\Property(property="amount", type="number", format="number",example="10"),
  *             @OA\Property(property="payment_method", type="string", format="string",example="bkash"),
 *            @OA\Property(property="payment_date", type="string", format="string",example="2019-06-29"),
 *  *  *            @OA\Property(property="note", type="string", format="string",example="note"),
 *  *            @OA\Property(property="restaurant_id", type="number", format="number",example="1"),
  *  *    *               @OA\Property(property="is_active", type="string", format="string",example="is_active"),
 *    *            @OA\Property(property="paid_by", type="string", format="string",example="paid_by"),
 *  *  *    *            @OA\Property(property="description", type="string", format="string",example="description"),
 *  *    *            @OA\Property(property="expense_type", type="string", format="string",example="expense type"),
 *  *  *  *    *            @OA\Property(property="supplier_id", type="string", format="string",example="supplier_id"),
 *  *    *            @OA\Property(property="reciepts", type="string", format="array",example={"a.jpg"}),
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

public function updateInvoicePayment(InvoicePaymentUpdateRequest $request)
{
    try {

        return  DB::transaction(function () use ($request) {

            $updatableData = $request->validated();

            $restaurantFound = Restaurant::where([
                "id" => $updatableData["restaurant_id"]
            ])
            ->first();
            if (!$restaurantFound) {
    return response()->json([
        "message" => "No Business Found with this id"
    ],404);
            }

            $invoiceDateWithTime = Carbon::parse($updatableData["payment_date"]);
            $invoiceDateWithTime->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
            $updatableData["payment_date"] =    $invoiceDateWithTime;

            if(empty($updatableData["paid_by"])) {
                $updatableData["paid_by"] = $request->user()->first_Name . " " . $request->user()->last_Name;
            }





             $expense = Expense::where([
                 "expenses.id" => $updatableData["id"]
             ])
         ->update([
            "expenses.description" => $updatableData["description"],
            "expenses.expense_type" => $updatableData["expense_type"],
            "expenses.supplier_id" => $updatableData["supplier_id"],
            "expenses.reciepts" => $updatableData["reciepts"],
             "expenses.amount" => $updatableData["amount"],
             "expenses.payment_method" => $updatableData["payment_method"],
             "expenses.payment_date" => $updatableData["payment_date"],
             "expenses.note" => $updatableData["note"], // Use an alias to specify the 'note' column
             "expenses.paid_by" => $updatableData["paid_by"],
             "expenses.is_active" => $updatableData["is_active"]
         ]);

         $expense = Expense::find($updatableData["id"]);

            return response($expense, 200);
        });
    } catch (Exception $e) {
        error_log($e->getMessage());
        return response()->json([
            "message" => "some thing went wrong",
            "original_message" => $e->getMessage()
        ],404);
    }
}


/**
 *
 * @OA\Get(
 *      path="/v1.0/expenses/{restaurant_id}/{perPage}",
 *      operationId="getInvoicePayments",
 *      tags={"expense_management"},
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
* name="order_by",
* in="query",
* description="order_by",
* required=true,
* example="ASC"
* ),
 * *  @OA\Parameter(
* name="search_key",
* in="query",
* description="search_key",
* required=true,
* example="search_key"
* ),
 * *  @OA\Parameter(
* name="min_amount",
* in="query",
* description="min_total_due",
* required=true,
* example="1"
* ),
 * *  @OA\Parameter(
* name="max_amount",
* in="query",
* description="max_total_due",
* required=true,
* example="1"
* ),
 * *  @OA\Parameter(
* name="payment_method",
* in="query",
* description="payment_method",
* required=true,
* example="1"
* ),
 * *  @OA\Parameter(
* name="supplier_id",
* in="query",
* description="supplier_id",
* required=true,
* example="1"
* ),
 * *  @OA\Parameter(
* name="is_active",
* in="query",
* description="is_active",
* required=true,
* example="1"
* ),

 *      summary="This method is to get expenses ",
 *      description="This method is to get expenses",
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

public function getInvoicePayments($restaurant_id, $perPage, Request $request)
{
    try {


        // $automobilesQuery = AutomobileMake::with("makes");

        $expenseQuery =   Expense::
        with("supplier")
        ->where([
            "restaurant_id" => $restaurant_id
        ])
        ->when(request()->filled("is_active"), function($query) {
            $query->where("expenses.is_active",request()->input("is_active"));
         });


        if (!empty($request->search_key)) {
            $expenseQuery = $expenseQuery->where(function ($query) use ($request) {
                $term = $request->search_key;
                $query->where("expenses.payment_method", "like", "%" . $term . "%");
                $query->orWhere("expenses.payment_method", "like", "%" . $term . "%");
                 $query->orWhere("expenses.amount", $term);
            });
        }

         if (!empty($request->supplier_id)) {
            $expenseQuery = $expenseQuery->where('expenses.supplier_id', $request->supplier_id);
        }
        if (!empty($request->payment_method)) {
            $expenseQuery = $expenseQuery->where('expenses.payment_method', $request->payment_method);
        }
        if (!empty($request->start_date)) {
            $expenseQuery = $expenseQuery->where('expenses.payment_date', ">=", $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expenseQuery = $expenseQuery->where('expenses.payment_date', "<=", $request->end_date);
        }

        if (!empty($request->min_amount)) {
            $expenseQuery = $expenseQuery->where('expenses.amount', ">=", $request->min_amount);
        }
        if (!empty($request->max_amount)) {
            $expenseQuery = $expenseQuery->where('expenses.amount', "<=", $request->max_amount);
        }


        $expenses = $expenseQuery
        ->select("expenses.*")
        ->orderBy("expenses.id",$request->order_by)
        ->paginate($perPage);

        return response()->json($expenses, 200);
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
 *      path="/v2.0/expenses/get/single/{id}",
 *      operationId="getInvoicePaymentByIdv2",
 *      tags={"expense_management"},
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

 *      summary="This method is to get expenses by id",
 *      description="This method is to get expenses by id",
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

 public function getInvoicePaymentByIdv2($id, Request $request)
 {
     try {

         $expense = Expense::with("supplier")->where([
             "expenses.generated_id" => $id,
         ])
         ->select("expenses.*")
         ->first();


         if(!$expense) {
        return response()->json([
           "message" => "no expenses found"
        ],404);

         }




         return response()->json($expense, 200);
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
 *      path="/v1.0/expenses/{id}",
 *      operationId="deleteInvoicePaymentByIdV2",
 *      tags={"expense_management"},
 *       security={
 *           {"bearerAuth": {}},
 *            {"pin": {}}
 *       },

 *              @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="id",
 *         required=true,
 *  example="1"
 *      ),
 *      summary="This method is to delete expenses by id",
 *      description="This method is to delete expenses by id",
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

 public function deleteInvoicePaymentByIdV2($id, Request $request)
 {

     try {



         $expense = Expense::where([
             "expenses.id" => $id,
         ])
         ->select("expenses.id")
         ->first();

         if(!$expense) {
      return response()->json([
 "message" => "no expenses  found"
 ],404);
         }



        $expense->delete();



         return response()->json(["ok" => true], 200);
     } catch (Exception $e) {

        return response()->json([
            "message" => "some thing went wrong",
            "original_message" => $e->getMessage()
        ],404);
     }
 }




}

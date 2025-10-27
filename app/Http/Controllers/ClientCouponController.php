<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Utils\DiscountUtil;
use App\Http\Utils\ErrorUtil;
use App\Http\Utils\UserActivityUtil;
use App\Models\Coupon;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ClientCouponController extends Controller
{

    use ErrorUtil, DiscountUtil;
     /**
     *
     * @OA\Get(
     *      path="/v1.0/client/coupons/by-business-id/{business_id}/{perPage}",
     *      operationId="getCouponsByBusinessIdClient",
     *      tags={"client.coupon"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
    *              @OA\Parameter(
     *         name="business_id",
     *         in="path",
     *         description="business_id",
     *         required=true,
     *  example="1"
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
     *      summary="This method is to get coupons by business id ",
     *      description="This method is to get coupons by business id",
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

    public function getCouponsByBusinessIdClient($business_id,$perPage, Request $request)
    {
        try {

            $this->storeActivity($request,"");

            $couponQuery = Coupon::where([
                "business_id" => $business_id,
                "is_active" => 1
            ])

        ->where('coupon_start_date', '<=', Carbon::now()->subDay())
        ->where('coupon_end_date', '>=', Carbon::now()->subDay());

            if (!empty($request->search_key)) {
                $couponQuery = $couponQuery->where(function ($query) use ($request) {
                    $term = $request->search_key;
                    $query->where("name", "like", "%" . $term . "%");
                    $query->orWhere("code", "like", "%" . $term . "%");
                });
            }

            if (!empty($request->start_date)) {
                $couponQuery = $couponQuery->where('created_at', ">=", $request->start_date);
            }
            if (!empty($request->end_date)) {
                $couponQuery = $couponQuery->where('created_at', "<=", $request->end_date);
            }

            $coupons = $couponQuery->orderByDesc("id")->paginate($perPage);

            return response()->json($coupons, 200);
        } catch (Exception $e) {

            return $this->sendError($e,500,$request);
        }
    }


       /**
     *
     * @OA\Get(
     *      path="/v1.0/client/coupons/all/{perPage}",
     *      operationId="getCouponsClient",
     *      tags={"client.coupon"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
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
     *      summary="This method is to get coupons",
     *      description="This method is to get coupons",
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

    public function getCouponsClient($perPage, Request $request)
    {
        try {
            $this->storeActivity($request,"");


            $couponQuery =  Coupon::where([
                "is_active" => 1
            ])

        ->where('coupon_start_date', '<=', Carbon::now()->subDay())
        ->where('coupon_end_date', '>=', Carbon::now()->subDay());

            if (!empty($request->search_key)) {
                $couponQuery = $couponQuery->where(function ($query) use ($request) {
                    $term = $request->search_key;
                    $query->where("name", "like", "%" . $term . "%");
                    $query->orWhere("code", "like", "%" . $term . "%");
                });
            }

            if (!empty($request->start_date)) {
                $couponQuery = $couponQuery->where('created_at', ">=", $request->start_date);
            }
            if (!empty($request->end_date)) {
                $couponQuery = $couponQuery->where('created_at', "<=", $request->end_date);
            }


            $coupons = $couponQuery->orderByDesc("id")->paginate($perPage);

            return response()->json($coupons, 200);
        } catch (Exception $e) {

            return $this->sendError($e,500,$request);
        }
    }

    /**
     *
     * @OA\Get(
     *      path="/v1.0/client/coupons/all-auto-applied-coupons/{business_id}",
     *      operationId="getAutoAppliedCouponsByBusinessIdClient",
     *      tags={"client.coupon"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *              @OA\Parameter(
     *         name="business_id",
     *         in="path",
     *         description="business_id",
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
     *      summary="This method is to get coupons",
     *      description="This method is to get coupons",
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

     public function getAutoAppliedCouponsByBusinessIdClient($business_id, Request $request)
     {
         try {
             $this->storeActivity($request,"");


             $couponQuery =  Coupon::where([
                 "is_active" => 1,
                 "business_id" => $business_id,
                 "is_auto_apply" => 1
             ])

         ->where('coupon_start_date', '<=', Carbon::now()->subDay())
         ->where('coupon_end_date', '>=', Carbon::now()->subDay());

             if (!empty($request->search_key)) {
                 $couponQuery = $couponQuery->where(function ($query) use ($request) {
                     $term = $request->search_key;
                     $query->where("name", "like", "%" . $term . "%");
                     $query->orWhere("code", "like", "%" . $term . "%");
                 });
             }

             if (!empty($request->start_date)) {
                 $couponQuery = $couponQuery->where('created_at', ">=", $request->start_date);
             }
             if (!empty($request->end_date)) {
                 $couponQuery = $couponQuery->where('created_at', "<=", $request->end_date);
             }


             $coupons = $couponQuery->orderByDesc("id")->get();

             return response()->json($coupons, 200);
         } catch (Exception $e) {

             return $this->sendError($e,500,$request);
         }
     }

     /**
     *
     * @OA\Get(
     *      path="/v1.0/client/coupons/single/{id}",
     *      operationId="getCouponByIdClient",
     *      tags={"client.coupon"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *              @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
     *  example="6"
     *      ),
     *      summary="This method is to get coupon by id ",
     *      description="This method is to get coupon by id ",
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

    public function getCouponByIdClient($id, Request $request)
    {
        try {

            $this->storeActivity($request,"");

            $coupon = Coupon::where([
                "id" => $id,
                "is_active" => 1
            ])

        ->where('coupon_start_date', '<=', Carbon::now()->subDay())
        ->where('coupon_end_date', '>=', Carbon::now()->subDay())
            ->first();

            if(!$coupon) {
                 return response()->json([
                    "message" => "coupon not found"
                 ],404);
            }


            return response()->json($coupon, 200);
        } catch (Exception $e) {

            return $this->sendError($e,500,$request);
        }
    }



       /**
     *
     * @OA\Get(
     *      path="/v1.0/client/coupons/get-discount/{business_id}/{code}/{amount}",
     *      operationId="getCouponDiscountClient",
     *      tags={"client.coupon"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *   *              @OA\Parameter(
     *         name="business_id",
     *         in="path",
     *         description="business_id",
     *         required=true,
     *  example="6"
     *      ),
     *              @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="code",
     *         required=true,
     *  example="6"
     *      ),
     * *              @OA\Parameter(
     *         name="amount",
     *         in="path",
     *         description="amount",
     *         required=true,
     *  example="6"
     *      ),
     *
     *      summary="This method is to check coupon",
     *      description="This method is to check coupon",
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

    public function getCouponDiscountClient($business_id,$code,$amount, Request $request)
    {
        try {
            $this->storeActivity($request,"");

        $discount = $this->getCouponDiscount($business_id,$code,$amount);

            if(!$discount) {
                 return response()->json([
                    "message" => "coupon not found"
                 ],404);
            }





            return response()->json($discount, 200);
        } catch (Exception $e) {

            return $this->sendError($e,500,$request);
        }
    }

}

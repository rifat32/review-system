<?php

namespace App\Http\Controllers;

use App\Models\DailyView;
use Illuminate\Http\Request;

class DailyViewsController extends Controller
{
    // ##################################################
    // This method is to store daily views
    // ##################################################
 /**
        *
     * @OA\Post(
     *      path="/dailyviews/{restaurantId}",
     *      operationId="store",
     *      tags={"daily_views"},
    *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store daily views",
     *      description="This method is to store daily views",
     *  @OA\Parameter(
* name="restaurantId",
* in="path",
* description="method",
* required=true,
* example="1"
* ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={   "view_date","daily_views"},
     *
     *             @OA\Property(property="view_date", type="string", format="string",example="2019-06-29"),
     *            @OA\Property(property="daily_views", type="string", format="string",example="1"),


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



    public function store($restaurantId, Request $request)
    {

        $body = $request->toArray();
        $body["restaurant_id"] = $restaurantId;

        $View  =  DailyView::create($body);

        return response($View, 200);
    }
    // ##################################################
    // This method is to update daily views
    // ##################################################

/**
        *
     * @OA\Patch(
     *      path="/dailyviews/update/{restaurantId}",
     *      operationId="update",
     *      tags={"daily_views"},
    *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update daily views",
     *      description="This method is to update daily views",
     *  @OA\Parameter(
* name="restaurantId",
* in="path",
* description="method",
* required=true,
* example="1"
* ),
   *            @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         description="method",
     *         required=false,
     * example="PATCH"
     *      ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={   "view_date"},
     *
     *             @OA\Property(property="view_date", type="string", format="string",example="2019-06-29"),
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






    public function update($restaurantId, Request $request)
    {

        $query = DailyView::where(["restaurant_id" => $restaurantId]);
        $view = $query->first();
        if ($view) {
            $updatedView =    tap($query)->update(
                [
                    "view_date" => $request->view_date,
                    "daily_views" => $view->daily_views + 1
                ]
            )->first();
            return response($updatedView, 200);
        }
        return response("no view found", 200);
    }
}

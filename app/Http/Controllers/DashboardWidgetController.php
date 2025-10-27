<?php

namespace App\Http\Controllers;

use App\Models\DashboardWidget;
use App\Models\UserDashboard;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    /**
        *
     * @OA\Post(
     *      path="/superadmin/dashboard-widget/create",
     *      operationId="createWidget",
     *      tags={"superadmin-dashboard-widget"},
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
     *            @OA\Property(property="user_type", type="string", format="string",example="admin")

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


    public function createWidget (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  DashboardWidget::create($body);
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Put(
     *      path="/superadmin/dashboard-widget/update",
     *      operationId="updateWidget",
     *      tags={"superadmin-dashboard-widget"},
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
     *            @OA\Property(property="user_type", type="string", format="string",example="admin")

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


    public function updateWidget (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  tap(DashboardWidget::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($dashboard_widget, 200);
    }
     /**
        *
     * @OA\Get(
     *      path="/superadmin/dashboard-widget/get",
     *      operationId="getWidget",
     *      tags={"superadmin-dashboard-widget"},
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

    public function getWidget (Request $request) {
        $dashboard_widget =  DashboardWidget::get();
        return response($dashboard_widget, 200);
    }

     /**
        *
     * @OA\Get(
     *      path="/superadmin/dashboard-widget/get/{id}",
     *      operationId="getWidgetById",
     *      tags={"superadmin-dashboard-widget"},
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

    public function getWidgetById ($id, Request $request) {
        $dashboard_widget =  DashboardWidget::where([
            "id" => $id
        ])->first();
        return response($dashboard_widget, 200);
    }

/**
        *
     * @OA\Delete(
     *      path="/superadmin/dashboard-widget/delete/{id}",
     *      operationId="deleteWidgetById",
     *      tags={"superadmin-dashboard-widget"},
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

    public function deleteWidgetById ($id, Request $request) {
          DashboardWidget::where([
            "id" => $id
        ])->delete();
        return response(["success"=>true], 200);
    }

     /**
        *
     * @OA\Post(
     *      path="/user-dashboard/create",
     *      operationId="createUserDashboard",
     *      tags={"user-dashboard"},
     *      security={
     *           {"bearerAuth": {}}
     *       },

     *      summary="This method is to store user dashboard",
     *      description="This method is to store user dashboard",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={"widget_id","order"},
     *
     *             @OA\Property(property="widget_id", type="string", format="string",example="1"),
     *            @OA\Property(property="order", type="string", format="string",example="1")

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
    public function createUserDashboard (Request $request) {
        $body = $request->toArray();
        $body['user_id'] = $request->user()->id;
        $dashboard_widget =  UserDashboard::create($body);
        return response($dashboard_widget, 200);
    }

 /**
        *
     * @OA\Put(
     *      path="/user-dashboard/update",
     *      operationId="updateUserDashboard",
     *      tags={"user-dashboard"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update user dashboard",
     *      description="This method is to update user dashboard",
     *

     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
       *            required={"id","widget_id","order"},
     **             @OA\Property(property="id", type="string", format="string",example="1"),
     *             @OA\Property(property="widget_id", type="string", format="string",example="1"),
     *            @OA\Property(property="order", type="string", format="string",example="1")

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
    public function updateUserDashboard (Request $request) {
        $body = $request->toArray();
        $dashboard_widget =  tap(UserDashboard::where([
            "id" => $body["id"]
        ]))
        ->update($body)
        ->first();
        return response($dashboard_widget, 200);
    }
      /**
        *
     * @OA\Get(
       *      path="/user-dashboard/get",
     *      operationId="getUserDashboard",
     *      tags={"user-dashboard"},
 *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to get user dashboard ",
     *      description="This method is to get user  dashboard ",
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

    public function getUserDashboard (Request $request) {
        $dashboard_widget =  UserDashboard::orderByDesc("order")->get();
        return response($dashboard_widget, 200);
    }

     /**
        *
     * @OA\Get(
     *      path="/user-dashboard/get/{id}",
     *      operationId="getUserDashboardById",
     *      tags={"user-dashboard"},
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
     *      summary="This method is to get user dashboard  by id",
     *      description="This method is to get single user dashboard  by id",
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

    public function getUserDashboardById ($id, Request $request) {
        $dashboard =  UserDashboard::where([
            "id" => $id
        ])->first();
        return response($dashboard, 200);
    }

    /**
        *
     * @OA\Delete(
     *      path="/user-dashboard/delete/{id}",
     *      operationId="deleteUserDashboardById",
     *      tags={"user-dashboard"},
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
     *      summary="This method is to delete user dashboard  by id",
     *      description="This method is to delete user single dashboard  by id",
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

    public function deleteUserDashboardById ($id, Request $request) {
        UserDashboard::where([
          "id" => $id
      ])->delete();
      return response(["success"=>true], 200);
  }



}

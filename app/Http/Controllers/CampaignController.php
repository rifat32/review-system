<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessOwnerToggleOptionsRequest;
use App\Http\Requests\CampaignCreateRequest;
use App\Http\Requests\CampaignUpdateRequest;
use App\Http\Utils\ErrorUtil;
use App\Models\Campaign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    use ErrorUtil;

    /**
     *
     * @OA\Post(
     *      path="/v1.0/campaigns",
     *      operationId="createCampaign",
     *      tags={"campaign_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to store campaign",
     *      description="This method is to store campaign",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={},
     *
*      @OA\Property(property="business_id", type="string", example="1"),
*      @OA\Property(property="name", type="string", example="Holiday Discount"),
*      @OA\Property(property="type", type="string", example="spend_certain_amount"),
*      @OA\Property(property="discount_type", type="string", format="string", example="fixed"),
*      @OA\Property(property="discount_amount", type="number", format="float", example=10.50),
*      @OA\Property(property="campaign_start_date", type="string", format="date", example="2025-01-01"),
*      @OA\Property(property="campaign_end_date", type="string", format="date", example="2025-12-31"),
*      @OA\Property(property="campaign_start_time", type="string", format="time", example="18:00"),
*      @OA\Property(property="spend_threshold", type="string", format="time", example="15"),
*      @OA\Property(property="campaign_end_time", type="string", format="time", example="22:00"),
*      @OA\Property(property="dish_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="free_dish_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="menu_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-19T10:00:00Z"),
*      @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-19T12:00:00Z"),

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

    public function createCampaign(CampaignCreateRequest $request)
    {
        try {
            $this->storeActivity($request, "");
            return DB::transaction(function () use ($request) {



                $request_data = $request->validated();

                $request_data["max_redemptions"] = 0;
                $request_data["customer_redemptions"] = 0;
                $request_data["is_active"] = 1;


                $code_exists = Campaign::where([
                    "business_id" => $request_data["business_id"],
                    "name" => $request_data["name"]
                ])->first();

                if ($code_exists) {
                    $error =  [
                        "message" => "The given data was invalid.",
                        "errors" => ["name" => ["This name is already taken"]]
                    ];
                    throw new Exception(json_encode($error), 422);
                }


                $campaign =  Campaign::create($request_data);

                $campaign->dishes()->sync($request_data["dish_ids"]);

                if(!empty($request_data["free_dish_ids"])) {
                   $campaign->free_dishes()->sync($request_data["free_dish_ids"]);
                }




                $campaign->menus()->sync($request_data["menu_ids"]);
                return response($campaign, 201);
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $this->sendError($e, 500, $request);
        }
    }

    /**
     *
     * @OA\Put(
     *      path="/v1.0/campaigns",
     *      operationId="updateCampaign",
     *      tags={"campaign_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to update campaigns",
     *      description="This method is to update campaigns",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={},
     *  *    @OA\Property(property="id", type="number", format="number",example="1"),
     *      @OA\Property(property="business_id", type="string", example="1"),
*      @OA\Property(property="name", type="string", example="Holiday Discount"),
*      @OA\Property(property="type", type="string", example="spend_certain_amount"),
*      @OA\Property(property="discount_type", type="string", format="string", example="fixed"),
*      @OA\Property(property="discount_amount", type="number", format="float", example=10.50),
*      @OA\Property(property="campaign_start_date", type="string", format="date", example="2025-01-01"),
*      @OA\Property(property="campaign_end_date", type="string", format="date", example="2025-12-31"),
*      @OA\Property(property="campaign_start_time", type="string", format="time", example="18:00"),
*      @OA\Property(property="spend_threshold", type="string", format="time", example="15"),


*      @OA\Property(property="campaign_end_time", type="string", format="time", example="22:00"),
*      @OA\Property(property="dish_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="free_dish_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="menu_ids", type="array", @OA\Items(type="integer", example=1), description="List of sub-service IDs associated with the campaign"),
*      @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-19T10:00:00Z"),
*      @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-19T12:00:00Z"),

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

    public function updateCampaign(CampaignUpdateRequest $request)
    {
        try {
            $this->storeActivity($request, "");
            return  DB::transaction(function () use ($request) {

                $request_data = $request->validated();

                // if (!$this->businessOwnerCheck($request_data["business_id"])) {
                //     return response()->json([
                //         "message" => "you are not the owner of the business or the requested business does not exist."
                //     ], 401);
                // }

                $code_exists = Campaign::where([
                    "business_id" => $request_data["business_id"],
                    "name" => $request_data["name"]
                ])
                    ->where('id', '<>', $request_data["id"])
                    ->first();

                if ($code_exists) {
                    $error =  [
                        "message" => "The given data was invalid.",
                        "errors" => ["code" => ["This code is already taken"]]
                    ];
                    throw new Exception(json_encode($error), 422);
                }


                $campaign = Campaign::where(["id" => $request_data["id"]])->first();

                if(empty($campaign)) {
                    throw new Exception("some thing went wrong");
                }

                $campaign->fill($request_data);

                $campaign->save();

                $campaign->dishes()->sync($request_data["dish_ids"]);

                if(!empty($request_data["free_dish_ids"])) {
                    $campaign->free_dishes()->sync($request_data["free_dish_ids"]);
                }



                $campaign->menus()->sync($request_data["menu_ids"]);


                return response($campaign, 201);
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $this->sendError($e, 500, $request);
        }
    }

    /**
     *
     * @OA\Put(
     *      path="/v1.0/campaigns/toggle-active",
     *      operationId="toggleActiveCampaign",
     *      tags={"business_management"},
     *       security={
     *           {"bearerAuth": {}}
     *       },
     *      summary="This method is to toggle campaign",
     *      description="This method is to toggle campaign",
     *
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id","first_Name","last_Name","email","password","password_confirmation","phone","address_line_1","address_line_2","country","city","postcode","role"},
     *           @OA\Property(property="id", type="string", format="number",example="1"),
     *  *           @OA\Property(property="business_id", type="string", format="number",example="1"),
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

    public function toggleActiveCampaign(BusinessOwnerToggleOptionsRequest $request)
    {

        try {
            $this->storeActivity($request, "");

            $request_data = $request->validated();

            // if (!$this->businessOwnerCheck($request_data["business_id"])) {
            //     return response()->json([
            //         "message" => "you are not the owner of the business or the requested business does not exist."
            //     ], 401);
            // }



            $campaign =  Campaign::where([
                "business_id" => $request_data["business_id"],
                "id" => $request_data["id"]
            ])
                ->first();



            $campaign->update([
                'is_active' => !$campaign->is_active
            ]);

            return response()->json(['message' => 'campaign status updated successfully'], 200);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $this->sendError($e, 500, $request);
        }
    }


    /**
     *
     * @OA\Get(
     *      path="/v1.0/campaigns/{business_id}/{perPage}",
     *      operationId="getCampaigns",
     *      tags={"campaign_management"},
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
     *      summary="This method is to get campaigns ",
     *      description="This method is to get campaigns",
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

     public function getCampaigns($business_id, $perPage, Request $request)
     {
         try {
             $this->storeActivity($request, "");

             // if (!$this->businessOwnerCheck($business_id)) {
             //     return response()->json([
             //         "message" => "you are not the owner of the business or the requested business does not exist."
             //     ], 401);
             // }

             $campaignQuery = Campaign::with("dishes","free_dishes","menus")->where([
                 "business_id" => $business_id
             ]);

             if (!empty($request->search_key)) {
                 $campaignQuery = $campaignQuery->where(function ($query) use ($request) {
                     $term = $request->search_key;
                     $query->where("name", "like", "%" . $term . "%");
                     $query->orWhere("type", "like", "%" . $term . "%");
                 });
             }

             if (!empty($request->start_date)) {
                 $campaignQuery = $campaignQuery->where('created_at', ">=", $request->start_date);
             }
             if (!empty($request->end_date)) {
                 $campaignQuery = $campaignQuery->where('created_at', "<=", $request->end_date);
             }

             if (!empty($request->type)) {
                $campaignQuery = $campaignQuery->where('type',  $request->type);
            }


             $campaignQuery = $campaignQuery->orderByDesc("id");

             if ($perPage == '0') {
                 $campaigns = $campaignQuery->get();
             } else {
                 $campaigns = $campaignQuery->paginate($perPage);
             }




             return response()->json($campaigns, 200);
         } catch (Exception $e) {

             return $this->sendError($e, 500, $request);
         }
     }

    /**
     *
     * @OA\Get(
     *      path="/v1.0/client/campaigns/{business_id}/{perPage}",
     *      operationId="getCampaignsClient",
     *      tags={"campaign_management"},
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
     *      summary="This method is to get campaigns ",
     *      description="This method is to get campaigns",
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

     public function getCampaignsClient($business_id, $perPage, Request $request)
     {
         try {
             $this->storeActivity($request, "");

             // if (!$this->businessOwnerCheck($business_id)) {
             //     return response()->json([
             //         "message" => "you are not the owner of the business or the requested business does not exist."
             //     ], 401);
             // }

             $campaignQuery = Campaign::with("dishes","free_dishes","menus.dishes")->where([
                 "business_id" => $business_id
             ]);

             if (!empty($request->search_key)) {
                 $campaignQuery = $campaignQuery->where(function ($query) use ($request) {
                     $term = $request->search_key;
                     $query->where("name", "like", "%" . $term . "%");
                     $query->orWhere("code", "like", "%" . $term . "%");
                 });
             }

             if (!empty($request->start_date)) {
                 $campaignQuery = $campaignQuery->where('created_at', ">=", $request->start_date);
             }
             if (!empty($request->end_date)) {
                 $campaignQuery = $campaignQuery->where('created_at', "<=", $request->end_date);
             }

             $campaignQuery = $campaignQuery->orderByDesc("id");

             if ($perPage == '0') {
                 $campaigns = $campaignQuery->get();
             } else {
                 $campaigns = $campaignQuery->paginate($perPage);
             }


             foreach ($campaigns as $campaign) {
                if (!in_array($campaign->type, ['spend_certain_amount', 'time_based_discount','menu_discount'])) {
                    continue; // Skip if the campaign type is not eligible
                }

                foreach ($campaign->dishes as $dish) {
                    $original_price = (float) $dish->price;

                    $take_away_calculated_price =
                    ((float) $dish->take_away_discounted_price > 0) ?
                        (float) $dish->take_away_discounted_price :
                        ((float) $dish->take_away ? (float) $dish->take_away :  $original_price);

                    $delivery_calculated_price =
                        ((float) $dish->delivery_discounted_price > 0) ?
                            (float) $dish->delivery_discounted_price :
                            ((float) $dish->delivery ? (float) $dish->delivery :  $original_price);

                    $eat_in_calculated_price = ((float) $dish->eat_in_discounted_price > 0) ? (float) $dish->eat_in_discounted_price : $original_price;


                    $dish->take_away_calculated_price = $take_away_calculated_price;
                    $dish->delivery_calculated_price = $delivery_calculated_price;
                    $dish->eat_in_calculated_price = $eat_in_calculated_price;

                    // Apply discount logic for the main price
                    if ($campaign->discount_type === 'percentage') {
                        $discounted_price = $original_price - ($original_price * ($campaign->discount_amount / 100));
                        $discounted_take_away_discounted_price = $take_away_calculated_price - ($original_price * ($campaign->discount_amount / 100));
                        $discounted_eat_in_discounted_price = $eat_in_calculated_price - ($original_price * ($campaign->discount_amount / 100));
                        $discounted_delivery_discounted_price = $delivery_calculated_price - ($original_price * ($campaign->discount_amount / 100));
                    } elseif ($campaign->discount_type === 'fixed') {
                        $discounted_price = max(0, $original_price - $campaign->discount_amount);
                        $discounted_take_away_discounted_price = max(0, $take_away_calculated_price - $campaign->discount_amount);
                        $discounted_eat_in_discounted_price = max(0, $eat_in_calculated_price - $campaign->discount_amount);
                        $discounted_delivery_discounted_price = max(0, $delivery_calculated_price - $campaign->discount_amount);
                    } else {
                        continue; // Skip if discount type is not recognized
                    }

                    // Assign the campaign discounted prices for all price fields
                    $dish->campaign_discounted_price = round($discounted_price, 2);
                    $dish->campaign_take_away_discounted_price = round($discounted_take_away_discounted_price, 2);
                    $dish->campaign_eat_in_discounted_price = round($discounted_eat_in_discounted_price, 2);
                    $dish->campaign_delivery_discounted_price = round($discounted_delivery_discounted_price, 2);
                }

                foreach ($campaign->menus as $menu) {
                    foreach ($menu->dishes as $dish) {
                        // Original prices
                        $original_price = $dish->price;
                        $take_away_discounted_price = $dish->take_away_discounted_price;
                        $eat_in_discounted_price = $dish->eat_in_discounted_price;
                        $delivery_discounted_price = $dish->delivery_discounted_price;

                        // Apply discount logic for the main price
                        if ($campaign->discount_type === 'percentage') {
                            $discounted_price = $original_price - ($original_price * ($campaign->discount_amount / 100));
                            $discounted_take_away_discounted_price = $take_away_discounted_price - ($original_price * ($campaign->discount_amount / 100));
                            $discounted_eat_in_discounted_price = $eat_in_discounted_price - ($original_price * ($campaign->discount_amount / 100));
                            $discounted_delivery_discounted_price = $delivery_discounted_price - ($original_price * ($campaign->discount_amount / 100));
                        } elseif ($campaign->discount_type === 'fixed') {
                            $discounted_price = max(0, $original_price - $campaign->discount_amount);
                            $discounted_take_away_discounted_price = max(0, $take_away_discounted_price - $campaign->discount_amount);
                            $discounted_eat_in_discounted_price = max(0, $eat_in_discounted_price - $campaign->discount_amount);
                            $discounted_delivery_discounted_price = max(0, $delivery_discounted_price - $campaign->discount_amount);
                        } else {
                            continue;
                        }

                        // Assign the campaign discounted prices for all price fields
                        $dish->campaign_discounted_price = round($discounted_price, 2);
                        $dish->campaign_take_away_discounted_price = round($discounted_take_away_discounted_price, 2);
                        $dish->campaign_eat_in_discounted_price = round($discounted_eat_in_discounted_price, 2);
                        $dish->campaign_delivery_discounted_price = round($discounted_delivery_discounted_price, 2);
                    }
                }

                $dish->calculated_price = $dish->campaign_discounted_price?$dish->campaign_discounted_price:$original_price;

                $dish->take_away_calculated_price = $dish->campaign_take_away_discounted_price?$dish->campaign_take_away_discounted_price:$take_away_calculated_price;

                $dish->delivery_calculated_price = $dish->campaign_delivery_discounted_price?$dish->campaign_delivery_discounted_price:$delivery_calculated_price;

                $dish->eat_in_calculated_price = $dish->campaign_eat_in_discounted_price?$dish->campaign_eat_in_discounted_price:$eat_in_calculated_price;


            }




             return response()->json($campaigns, 200);
         } catch (Exception $e) {

             return $this->sendError($e, 500, $request);
         }
     }

    /**
     *
     * @OA\Get(
     *      path="/v1.0/campaigns/single/{business_id}/{id}",
     *      operationId="getCampaignById",
     *      tags={"campaign_management"},
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
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
     *  example="6"
     *      ),
     *      summary="This method is to get campaign by id ",
     *      description="This method is to get campaign by id ",
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

    public function getCampaignById($business_id, $id, Request $request)
    {
        try {
            $this->storeActivity($request, "");

            // if (!$this->businessOwnerCheck($business_id)) {
            //     return response()->json([
            //         "message" => "you are not the owner of the business or the requested business does not exist."
            //     ], 401);
            // }

            $campaign = Campaign::
            with("dishes","free_dishes","menus")
            ->where([
                "business_id" => $business_id,
                "id" => $id
            ])
                ->first();

            if (!$campaign) {
                return response()->json([
                    "message" => "campaign not found"
                ], 404);
            }


            return response()->json($campaign, 200);
        } catch (Exception $e) {

            return $this->sendError($e, 500, $request);
        }
    }



    /**
     *
     *     @OA\Delete(
     *      path="/v1.0/campaigns/{business_id}/{id}",
     *      operationId="deleteCampaignById",
     *      tags={"campaign_management"},
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
     *  *              @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id",
     *         required=true,
     *  example="1"
     *      ),
     *      summary="This method is to delete campaign by id",
     *      description="This method is to delete campaign by id",
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

    public function deleteCampaignById($business_id, $id, Request $request)
    {

        try {

            $this->storeActivity($request, "");



            $campaign = Campaign::where([
                "business_id" => $business_id,
                "id" => $id
            ])
                ->first();
            if (!$campaign) {
                return response()->json([
                    "message" => "campaign not found"
                ], 404);
            }

            $campaign->delete();




            return response()->json(["ok" => true], 200);
        } catch (Exception $e) {

            return $this->sendError($e, 500, $request);
        }
    }
}

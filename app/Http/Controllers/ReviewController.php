<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Review;
use App\Models\ReviewValue;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // ##################################################
    // This method is to store variation  ReviewValue
    // ##################################################
    public function store($restaurantId, $rate, Request $request)
    {

        ReviewValue::where([
            "restaurant_id" => $restaurantId,
            "rate" => $rate
        ])
            ->delete();

        $reviewValues = $request->reviewvalue;
        $raviewValue_array = [];
        foreach ($reviewValues as $reviewValue) {
            $reviewValue["restaurant_id"] = $restaurantId;
            $reviewValue["rate"] = $rate;
            $createdReviewValue =  ReviewValue::create($reviewValue);
            array_push($raviewValue_array, $createdReviewValue);
        }

        return response($raviewValue_array, 201);
    }
    // ##################################################
    // This method is to get   ReviewValue
    // ##################################################
    public function getReviewValues($restaurantId, $rate, Request $request)
    {
        // with
        $reviewValues = ReviewValue::where([
            "restaurant_id" => $restaurantId,
            "rate" => $rate,

        ])
            ->get();


        return response($reviewValues, 200);
    }
    // ##################################################
    // This method is to get ReviewValue by id
    // ##################################################
    public function getreviewvalueById($restaurantId, Request $request)
    {
        // with
        $reviewValues = ReviewValue::where([
            "restaurant_id" => $restaurantId
        ])
            ->first();


        return response($reviewValues, 200);
    }
    // ##################################################
    // This method is to get average
    // ##################################################
    public function  getAverage($restaurantId, $start, $end, Request $request)
    {
        // with
        $reviews = Review::where([
            "restaurant_id" => $restaurantId
        ])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $data["total"]   = $reviews->count();
        $data["one"]   = 0;
        $data["two"]   = 0;
        $data["three"] = 0;
        $data["four"]  = 0;
        $data["five"]  = 0;
        foreach ($reviews as $review) {
            switch ($review->rate) {
                case 1:
                    $data["one"] += 1;
                    break;
                case 2:
                    $data["two"] += 1;
                    break;
                case 3:
                    $data["three"] += 1;
                    break;
                case 4:
                    $data["four"] += 1;
                    break;
                case 5:
                    $data["five"] += 1;
                    break;
            }
        }


        return response($data, 200);
    }
    // ##################################################
    // This method is to store review2
    // ##################################################
    public function store2($restaurantId, Request $request)
    {

        ReviewValue::where([
            "restaurant_id" => $restaurantId,
            "rate" => $request->rate
        ])
            ->delete();
        $reviewValue = [
            "tag" => $request->tag,
            "rate" => $request->rate,
            "restaurant_id" => $restaurantId
        ];

        $createdReviewValue =  ReviewValue::create($reviewValue);



        return response($createdReviewValue, 201);
    }
    // ##################################################
    // This method is to filter   Review
    // ##################################################
    public function  filterReview($restaurantId, $rate, $start, $end, Request $request)
    {
        // with
        $reviewValues = Review::where([
            "restaurant_id" => $restaurantId,
            "rate" => $rate
        ])
            ->whereBetween('created_at', [$start, $end])
            ->get();


        return response($reviewValues, 200);
    }
    // ##################################################
    // This method is to get review by restaurant id
    // ##################################################
    public function  getReviewByRestaurantId($restaurantId, Request $request)
    {
        // with
        $reviewValue = Review::where([
            "restaurant_id" => $restaurantId,
        ])
            ->get();


        return response($reviewValue, 200);
    }
    // ##################################################
    // This method is to get customer review
    // ##################################################
    public function  getCustommerReview($restaurantId, $start, $end, Request $request)
    {
        // with
        $data["reviews"] = Review::where([
            "restaurant_id" => $restaurantId,
        ])
            ->whereBetween('created_at', [$start, $end])
            ->get();
        $data["total"]   = $data["reviews"]->count();
        $data["one"]   = 0;
        $data["two"]   = 0;
        $data["three"] = 0;
        $data["four"]  = 0;
        $data["five"]  = 0;
        foreach ($data["reviews"]  as $reviewValue) {
            switch ($reviewValue->rate) {
                case 1:
                    $data["one"] += 1;
                    break;
                case 2:
                    $data["two"] += 1;
                    break;
                case 3:
                    $data["three"] += 1;
                    break;
                case 4:
                    $data["four"] += 1;
                    break;
                case 5:
                    $data["five"] += 1;
                    break;
            }
        }

        return response($data, 200);
    }

    // ##################################################
    // This method is to store review
    // ##################################################
    public function storeReview($restaurantId,  Request $request)
    {

        $review = [
            'description' => $request->description,
            'restaurant_id' => $restaurantId,
            'rate' => $request->rate,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,

        ];
        Review::create($review);


        return response($review, 201);
    }
}

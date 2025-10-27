<?php

namespace App\Http\Utils;


use App\Models\Coupon;
use Exception;
use Illuminate\Support\Facades\DB;

trait DiscountUtil
{




    // this function do all the task and returns transaction id or -1
    public function getCouponDiscount($garage_id, $code, $amount)
    {

        $coupon =  Coupon::where([
            "business_id" => $garage_id,
            "code" => $code,
            "is_active" => 1,

        ])
            // ->where('coupon_start_date', '<=', Carbon::now()->subDay())
            // ->where('coupon_end_date', '>=', Carbon::now()->subDay())
            ->first();

        if (!$coupon) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => ["coupon_code" => "no coupon is found"]
            ];
            throw new Exception(json_encode($error), 422);
        }

        if (!empty($coupon->min_total) && ($coupon->min_total > $amount)) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => ["coupon_code" => "minimim limit is " . $coupon->min_total]
            ];
            throw new Exception(json_encode($error), 422);
        }
        if (!empty($coupon->max_total) && ($coupon->max_total < $amount)) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => "maximum limit is " . $coupon->max_total
            ];
            throw new Exception(json_encode($error), 422);
        }

        if (!empty($coupon->redemptions) && $coupon->redemptions == $coupon->customer_redemptions) {
            $error = [
                "message" => "The given data was invalid.",
                "errors" => "maximum people reached"
            ];
            throw new Exception(json_encode($error), 422);
        }
        return $coupon;
    }


    public function canculate_discount($total_price, $discount_type, $discount_amount)
    {
        if (!empty($discount_type) && !empty($discount_amount)) {
            if ($discount_type == "fixed") {
                return $discount_amount;
            } else if ($discount_type == "percentage") {
                return ($total_price / 100) * $discount_amount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function canculate_discount_amount($total_price, $discount_type, $discount_amount)
    {
        if (!empty($discount_type) && !empty($discount_amount)) {
            if ($discount_type == "fixed") {
                return $discount_amount;
            } else if ($discount_type == "percentage") {
                return ($total_price / 100) * $discount_amount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }



    public function calculateCampaigns($dish) {
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


        $applicable_campaigns = collect();

        // Get campaigns from dish and its menu
        if ($dish->campaigns) {
            $applicable_campaigns = $applicable_campaigns->merge($dish->campaigns);
        }
        if ($dish->menu && $dish->menu->campaigns) {
            $applicable_campaigns = $applicable_campaigns->merge($dish->menu->campaigns);
        }

        // Filter valid campaigns based on type
        $valid_campaigns = $applicable_campaigns->filter(function ($campaign) {
            return in_array($campaign->type, ['spend_certain_amount', 'time_based_discount', 'menu_discount']);
        });

        foreach ($valid_campaigns as $campaign) {
            if ($campaign->discount_type === 'percentage') {
                $discounted_price = $original_price - ($original_price * ($campaign->discount_amount / 100));
                $discounted_take_away_price = $take_away_calculated_price - ($take_away_calculated_price * ($campaign->discount_amount / 100));
                $discounted_eat_in_price = $eat_in_calculated_price - ($eat_in_calculated_price * ($campaign->discount_amount / 100));
                $discounted_delivery_price = $delivery_calculated_price - ($delivery_calculated_price * ($campaign->discount_amount / 100));
            } elseif ($campaign->discount_type === 'fixed') {
                $discounted_price = max(0, $original_price - $campaign->discount_amount);
                $discounted_take_away_price = max(0, $take_away_calculated_price - $campaign->discount_amount);
                $discounted_eat_in_price = max(0, $eat_in_calculated_price - $campaign->discount_amount);
                $discounted_delivery_price = max(0, $delivery_calculated_price - $campaign->discount_amount);
            } else {
                continue;
            }

            // Assign campaign discounted prices
            $dish->campaign_discounted_price = round($discounted_price, 2);
            $dish->campaign_take_away_discounted_price = round($discounted_take_away_price, 2);
            $dish->campaign_eat_in_discounted_price = round($discounted_eat_in_price, 2);
            $dish->campaign_delivery_discounted_price = round($discounted_delivery_price, 2);
        }

        $dish->calculated_price = $dish->campaign_discounted_price?$dish->campaign_discounted_price:$original_price;

        $dish->take_away_calculated_price = $dish->campaign_take_away_discounted_price?$dish->campaign_take_away_discounted_price:$take_away_calculated_price;

        $dish->delivery_calculated_price = $dish->campaign_delivery_discounted_price?$dish->campaign_delivery_discounted_price:$delivery_calculated_price;

        $dish->eat_in_calculated_price = $dish->campaign_eat_in_discounted_price?$dish->campaign_eat_in_discounted_price:$eat_in_calculated_price;
        return $dish;
    }



}

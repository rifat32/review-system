<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'business_id' => 'required|exists:businesses,id',
        'name' => 'required|string|max:255',
        'type' => 'required|in:buy_one_get_one_same,buy_one_get_one_other,spend_certain_amount,time_based_discount,menu_discount',
        'discount_amount' => 'nullable|required_if:type,spend_certain_amount,time_based_discount,menu_discount|numeric|min:0',
        'discount_type' => 'nullable|required_if:type,spend_certain_amount,time_based_discount,menu_discount|in:fixed,percentage',
        'spend_threshold' => 'nullable|required_if:type,spend_certain_amount|numeric|min:0',
        'campaign_start_date' => 'required|date',
        'campaign_end_date' => 'required|date|after:campaign_start_date',
   'campaign_start_time' => 'nullable|required_if:type,time_based_discount|date_format:H:i:s',
'campaign_end_time' => 'nullable|required_if:type,time_based_discount|date_format:H:i:s',

          "free_dish_ids" => "nullable|array",
          "free_dish_ids.*" => "numeric|exists:dishes,id",

        "dish_ids" => "present|array",
        "dish_ids.*" => "numeric|exists:dishes,id",


        "menu_ids" => "present|array",
        "menu_ids.*" => "numeric|exists:menus,id",

        ];
    }
}

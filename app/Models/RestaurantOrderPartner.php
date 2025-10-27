<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderPartner extends Model
{
    use HasFactory;



    protected $fillable = [
        "restaurant_partner_id",
        'delivery',
        'delivery_order_commission',
        'delivery_shop_link',
        'eat_in',
        'eat_in_order_commission',
        'eat_in_shop_link',
        'takeaway',
        'takeaway_order_commission',
        'takeaway_link',
        'contact_details',
        "restaurant_id",
        "api_key",
        "payment_terms",
        "is_active",

    ];



    protected $casts = [
        'contact_details' => 'array',
    ];

    public function restaurant_partner() {
        return $this->hasOne(RestaurantPartner::class,"id","restaurant_partner_id");
    }


}

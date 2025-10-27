<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOrderPartnerSale extends Model
{
    use HasFactory;

    protected $fillable = [
        "restaurant_partner_id",
        'eat_in_orders',
        'eat_in_orders_amount',
        'takeaway_orders',
        'takeaway_orders_amount',
        'notes',
        'bank_payment',
        'cash_payment',
        'delivery_orders',
        'delivery_orders_amount',
        'restaurant_id'
    ];

    public function restaurant_partner() {
        return $this->hasOne(RestaurantPartner::class,"id","restaurant_partner_id");
    }

}

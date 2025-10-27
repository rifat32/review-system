<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [


        "order_app",
        "amount",
        "total_due_amount",
        "tax",
        "table_number",
        "restaurant_id",
        "payment_status",
        "status",
        "discount",
        "discount_type",
        "payment_method",
        "remarks",
        "type",
        "autoprint",
        "order_by",
        "customer_id",
        "customer_name",
        "cash",
        "card",
        "customer_phone",
        "customer_post_code",
        "customer_address",
        "door_no",
        "request_object",
        "initial_note",
        "customer_note",
        "order_time",
        "payment_intent_id"


    ];


    public function feedbacks()
    {
        return $this->hasMany(ReviewNew::class, 'booking_id', 'id');
    }


    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = round($value, 2);
    }


    public function setTableNumberAttribute($value)
    {
        $this->attributes['table_number'] = round($value, 2);
    }


    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = round($value, 2);
    }

    public function setCashAttribute($value)
    {
        $this->attributes['cash'] = round($value, 2);
    }

    public function setCardAttribute($value)
    {
        $this->attributes['card'] = round($value, 2);
    }




    public function detail()
    {
        return $this->hasMany(OrderDetail::class, "order_id", "id");
    }

    public function user()
    {
        return $this->hasOne(User::class, "id", "customer_id");
    }
    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, "id", "restaurant_id");
    }
    public function ordervariation()
    {
        return $this->hasMany(OrderVariation::class, "order_id", "id");
    }
    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    // ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "custom_id",
        "type",
        "qty",
        "order_id",
        "dish_id",
        "meal_id",
        'dish_price',
        "main_price"
     ];


     public function getDishPriceAttribute($value)
     {
         return round($value, 2);
     }

     public function getQtyAttribute($value)
     {
         return round($value, 2);
     }


     public function dish() {
        return $this->hasOne(Dish::class,"id","dish_id");
    }


    public function meal() {
        return $this->hasOne(Dish::class,"id","meal_id");
    }



    public function meal_variations() {
        return $this->hasMany(OrderDetailsDishes::class,"order_details_id","id");
    }


    public function variations() {
        return $this->hasMany(OrderDetailsVariation::class,"order_details_id","id");
    }





    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailsDishes extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_details_id",
        "dish_id",

     ];
     public function dish_variation() {
        return $this->hasMany(OrderDetailsVariation::class,"order_details_dish_id","id");
    }
    public function dish() {
        return $this->hasOne(Dish::class,"id","dish_id");
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

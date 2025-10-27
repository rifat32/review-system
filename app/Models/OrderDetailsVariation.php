<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailsVariation extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_details_id",
        'order_details_dish_id',
        "variation_id",
        "qty"
     ];

     public function variation() {
        return $this->hasOne(Variation::class,"id","variation_id");
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

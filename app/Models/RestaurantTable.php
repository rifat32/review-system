<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        "restaurant_id",
        "status",
        "table_no",
        "order_id",

    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

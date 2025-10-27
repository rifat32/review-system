<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishTimeSlot extends Model
{
    use HasFactory;
    protected $fillable = [
        'dish_id',
        'is_active',
        'day_of_week',
        'start_time',
        'end_time'
    ];
    
}

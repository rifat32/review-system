<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'tag',
        "restaurant_id",
        "is_default",
        "is_active"
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        "restaurant_id",
    ];
}

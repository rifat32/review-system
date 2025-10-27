<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "user_type"
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}

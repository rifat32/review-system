<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaflet extends Model
{
    use HasFactory;

    protected $fillable= [
        "restaurant_id",
        "thumbnail",
        "leaflet_data",
        "title",
        "type"
    ];


}

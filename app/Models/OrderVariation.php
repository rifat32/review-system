<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_id",
        "dish_id",
        "variation_id",
     ];
     public function variation() {
        return $this->hasMany(Variation::class,"id","variation_id");
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

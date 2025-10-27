<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    public function dish() {
        return $this->hasOne(Dish::class,"id","dish_id");
    }

    protected $fillable = [
        "deal_id",
        "dish_id"
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

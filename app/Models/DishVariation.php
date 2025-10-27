<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishVariation extends Model
{
    use HasFactory;
    protected $fillable = [
        "no_of_varation_allowed",
        'minimum_variation_required',
        "type_id",
        "dish_id",
        "order_number"
    ];


    public function variation_type() {
        return $this->belongsTo(VariationType::class,"type_id","id");
    }
    public function dish() {
        return $this->hasOne(Dish::class,"id","dish_id");
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];









}

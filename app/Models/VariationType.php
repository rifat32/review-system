<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationType extends Model
{
    protected $table = "variation_types";
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "restaurant_id",
        "order_number"
    ];

    public function variation() {
        return $this->hasMany(Variation::class,"type_id","id");
    }

    public function dish_variation() {
        return $this->hasMany(DishVariation::class,"type_id","id")->orderBy('dish_variations.order_number', 'asc');
    }














    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    protected static function boot()
    {
        parent::boot();

        static::created(function ($variationType) {
            $count = VariationType::where('restaurant_id', $variationType->restaurant_id)->count();
            $variationType->order_number = $count;
            $variationType->save();
        });
    }



}

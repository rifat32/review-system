<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [

        "name",

        "price",

        "take_away_discounted_price",
        "eat_in_discounted_price",
        "delivery_discounted_price",

        "restaurant_id",
        "menu_id",
        "image",
        "description",
        "take_away",
        "delivery",
        "type",
        "ingredients",
        "calories",
        "order_number",
        "preparation_time",

        "is_time_based",
        "show_in_future_date"
    ];

    public function scopeFilter($query,$restaurant)
{
    return $query->when(
        request()->has('day_of_week') || request()->has('time'),
        function ($q) use($restaurant) {
            $q->whereHas('time_slots', function ($slotQuery) use($restaurant) {

                    $time = NULL;
                    $day_of_week = NULL;
             if(!empty($restaurant->time_zone)) {
               $time = Carbon::now($restaurant->time_zone);
               $day_of_week = $time->dayOfWeek; // e.g. "monday"
            }
            $slotQuery->where('is_active', 1);
                   
                if (request()->has('day_of_week')) {
                    $slotQuery->where('day_of_week', $day_of_week?$day_of_week:request()->day_of_week);
                }
                if (request()->has('time')) {
                    $slotQuery->where('start_time', '<=', $time?$time:request()->time)
                              ->where('end_time', '>=', $time?$time:request()->time);
                }
            }) ->orWhere('is_time_based', 0);
        }
    )
    ->when(request()->filled('show_in_future_date'), function ($q) {
        if(request()->boolean('show_in_future_date')) {
            $q->where('show_in_future_date', 1);
        } else {
            $q->where('show_in_future_date', 0);
        }
    });
}          
         
        


    public function time_slots()
    {
        return $this->hasMany(DishTimeSlot::class, 'dish_id');
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_dishes', 'dish_id', 'campaign_id')
            ->where('campaigns.is_active', 1);
    }


    // public function deal() {
    //     return $this->hasOne(Deal::class,"dish_id","id");
    // }
    public function deal()
    {
        return $this->hasMany(Deal::class, "deal_id", "id");
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function dish_variations()
    {
        return $this->hasMany(DishVariation::class, 'dish_id', 'id')->orderBy('dish_variations.order_number', 'asc');
    }


    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($dish) {
            $count = Dish::where('restaurant_id', $dish->restaurant_id)
                ->where("menu_id", $dish->menu_id)

                ->count();
            $dish->order_number = $count;
            $dish->save();
        });
    }
}

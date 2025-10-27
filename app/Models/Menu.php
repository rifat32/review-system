<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "restaurant_id",
        "icon",
        "order_number",
        "show_in_customer",
        "is_time_based"
    ];



    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($menu) {
            $menuCount = Menu::where('restaurant_id', $menu->restaurant_id)->count();
            $menu->order_number = $menuCount;
            $menu->save();
        });
    }

    public function time_slots(): HasMany
    {
        return $this->hasMany(MenuTimeSlot::class, 'menu_id');
    }


    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_menus', 'menu_id', 'campaign_id')
            ->where('campaigns.is_active', 1);
    }


    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class, 'menu_id', 'id');
    }


    // Filter menus
    public function scopeFilter($query, $restaurant)
    {
        return $query->when(
            request()->has('day_of_week') || request()->has('time'),
            function ($q) use ($restaurant) {
                $q->whereHas('time_slots', function ($slotQuery) use ($restaurant) {

                    $time = NULL;
                    $day_of_week = NULL;
                    if (!empty($restaurant->time_zone)) {
                        $time = Carbon::now($restaurant->time_zone);
                        $day_of_week = $time->dayOfWeek;
                    }

                    $slotQuery->where('is_active', 1);

                    if (request()->has('day_of_week')) {
                        $slotQuery->where('day_of_week', $day_of_week ?? request()->day_of_week);
                    }

                    if (request()->has('time')) {
                        $slotTime = $time ? $time->format('H:i:s') : request()->time;
                        $slotQuery->where('start_time', '<=', $slotTime)
                            ->where('end_time', '>=', $slotTime);
                    }
                })->orWhere('is_time_based', 0);
            }
        );
    }
}

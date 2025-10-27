<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;


    protected $fillable = [
        'business_id',
        'name',
        'type',
        'spend_threshold',
        'discount_type',
        'discount_amount',
        'max_redemptions',
        'customer_redemptions',
        'campaign_start_date',
        'campaign_end_date',
        'campaign_start_time',
        'campaign_end_time',
        'is_active',
    ];


    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'campaign_dishes','campaign_id','dish_id');
    }

    public function free_dishes()
    {
        return $this->belongsToMany(Dish::class, 'campaign_free_dishes','campaign_id','dish_id');
    }




    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'campaign_menus','campaign_id','menu_id');
    }









}

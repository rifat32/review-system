<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "businesses";

    protected $fillable = [
        "Name",
        "Address",
        "PostCode",
        "OwnerID",
        "Status",
        "Logo",
        "Key_ID",
        "expiry_date",
        "enable_question",
        "About",
        "totalTables",
        "Webpage",
        "PhoneNumber",
        "EmailAddress",
        "homeText",
        "AdditionalInformation",
        "GoogleMapApi",
        'is_eat_in',
        'is_delivery',
        'is_take_away',
        'is_customer_order',
        'review_type',
        "show_image",
        "tax_percentage",
        'google_map_iframe',
        'Is_guest_user',
        'is_review_silder',
        'review_only',
        "is_business_type_restaurant",
        "business_type",
        "header_image",
        "menu_pdf",
        "rating_page_image",
        "placeholder_image",
        "is_pdf_manu",

        "primary_color",
        "secondary_color",

        "client_primary_color",
        "client_secondary_color",
        "client_tertiary_color",
        "user_review_report",
        "guest_user_review_report",
        "pin",
        "is_customer_schedule_order",
        "time_zone"
    ];

    protected $casts = [
        "eat_in_payment_mode" => "array",
        "takeaway_payment_mode" => "array",
        "delivery_payment_mode" => "array",
      ];


      public function menus() {
        return $this->hasMany(Menu::class,'restaurant_id','id');
    }

    public function dishes() {
        return $this->hasMany(Dish::class,'restaurant_id','id');
    }

    public function times() {
        return $this->hasMany(BusinessDay::class,'business_id','id');
    }


    public function customers(){
        return $this->belongsToMany(User::class, "orders",'restaurant_id', 'customer_id');
    }




    public function owner() {
        return $this->hasOne(User::class,'id','OwnerID');
    }
    public function table() {
        return $this->hasMany(RestaurantTable::class,'id','restaurant_id');
    }
    protected $hidden = [
      "pin",
      "STRIPE_KEY",
      "STRIPE_SECRET"

    ];
}

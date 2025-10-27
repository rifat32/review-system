<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;


    protected $fillable = [
        "description",
        "expense_type",
        "reciepts",
        "supplier_id",

        "amount",
        "payment_method",
        "payment_date",
        "note",
        "shareable_link",
        "paid_by",
        "is_active",

        "restaurant_id"
    ];

    protected $casts = [
        'reciepts' => 'array',
    ];

  public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }



}

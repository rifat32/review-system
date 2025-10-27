<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'note',
        'is_active',
        'restaurant_id'
    ];

     public function expenses()
    {
        return $this->hasMany(Expense::class, 'supplier_id', 'id');
    }
}

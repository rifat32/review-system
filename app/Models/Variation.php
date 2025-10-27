<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "type_id",
        "price",
    ];

    public function variation_type() {
        return $this->belongsTo(VariationType::class,"type_id","id");
    }
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

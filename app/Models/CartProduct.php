<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    
    protected $fillable = [
        'user_id',
        'invoice',
        'name',
        'quantity',
        'price',
        'bar_code',
    ];

}

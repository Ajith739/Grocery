<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'name',
        'category',
        'price',
        'originalPrice',
        'weight',
        'rating',
        'reviews',
        'brand',
        'discount',
        'bgColor'
    ];
}

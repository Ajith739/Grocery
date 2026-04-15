<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_code',
        'name',
        'category_id',
        'price',
        'originalPrice',
        'weight',
        'rating',
        'reviews',
        'brand',
        'discount',
        'bgColor',
        'quantity'
    ];


    // ✅ Relation (even without FK, still works)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public static function generateProductCode(): string
    {
        // Get last product_code
        $lastProduct = self::orderBy('id', 'desc')->first();

        if (!$lastProduct || !$lastProduct->product_code) {
            return 'p001';
        }

        // Extract number (p001 → 1)
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastProduct->product_code);

        // Increment
        $nextNumber = $lastNumber + 1;

        // Format with leading zeros
        return 'p' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}

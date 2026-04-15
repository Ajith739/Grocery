<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // ✅ Relation (even without FK, still works)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
                    ->withPivot('amount_needed')
                    ->withTimestamps();
    }

    public function getIsActuallyAvailableAttribute()
    {
        if (!$this->is_available) return false;
        
        $ingredients = $this->ingredients;
        if ($ingredients->isEmpty()) return true;

        foreach ($ingredients as $ingredient) {
            if ($ingredient->operational_stock < $ingredient->pivot->amount_needed) {
                return false;
            }
        }

        return true;
    }

    public function getMaxQuantityAttribute()
    {
        $ingredients = $this->ingredients;
        if ($ingredients->isEmpty()) return 99;

        $max = 999;
        foreach ($ingredients as $ingredient) {
            if ($ingredient->pivot->amount_needed > 0) {
              $possible = floor($ingredient->operational_stock / $ingredient->pivot->amount_needed);
              $max = min($max, $possible);
            }
        }

        return $max;
    }
}

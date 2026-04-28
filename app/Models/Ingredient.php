<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'warehouse_stock',
        'operational_stock',
    ];

    /**
     * Relationship to products using this ingredient.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
                    ->withPivot('amount_needed')
                    ->withTimestamps();
    }
}

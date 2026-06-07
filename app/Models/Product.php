<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected ?bool $cachedActualAvailability = null;
    protected ?int $cachedMaxQuantity = null;
    protected ?string $cachedThumbnailImage = null;

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

    public function addons()
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function getThumbnailImageAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if ($this->cachedThumbnailImage !== null) {
            return $this->cachedThumbnailImage;
        }

        $thumbnailPath = 'products/thumbs/' . basename($this->image);

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return $this->cachedThumbnailImage = $thumbnailPath;
        }

        return $this->cachedThumbnailImage = $this->image;
    }

    public function getIsActuallyAvailableAttribute()
    {
        if ($this->cachedActualAvailability !== null) {
            return $this->cachedActualAvailability;
        }

        if (!$this->is_available) return false;
        
        $ingredients = $this->loadedIngredients();

        if ($ingredients->isEmpty()) {
            return $this->cachedActualAvailability = true;
        }

        foreach ($ingredients as $ingredient) {
            if ($ingredient->operational_stock < $ingredient->pivot->amount_needed) {
                return $this->cachedActualAvailability = false;
            }
        }

        return $this->cachedActualAvailability = true;
    }

    public function getMaxQuantityAttribute()
    {
        if ($this->cachedMaxQuantity !== null) {
            return $this->cachedMaxQuantity;
        }

        $ingredients = $this->loadedIngredients();

        if ($ingredients->isEmpty()) {
            return $this->cachedMaxQuantity = 99;
        }

        $max = 999;
        foreach ($ingredients as $ingredient) {
            if ($ingredient->pivot->amount_needed > 0) {
              $possible = floor($ingredient->operational_stock / $ingredient->pivot->amount_needed);
              $max = min($max, $possible);
            }
        }

        return $this->cachedMaxQuantity = $max;
    }

    private function loadedIngredients()
    {
        return $this->relationLoaded('ingredients')
            ? $this->getRelation('ingredients')
            : $this->ingredients()->get();
    }
}

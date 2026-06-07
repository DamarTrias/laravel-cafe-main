<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        // QR Code detection
        if ($request->filled('table')) {
            session(['table_number' => 'Meja ' . $request->table]);
        }

        $categories = Category::select('id', 'name')->get();
        $query = Product::with([
            'category:id,name',
            'ingredients:id,operational_stock',
            'addons' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
        ])->where('is_available', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%$search%"));
            });
        }

        $products = $query->latest()->get();
        $mobileGroups = $this->mobileGroups($categories, $products);
        $searchCategoryId = $this->searchCategoryId($categories, $request->search);

        return view('pelanggan.store.index', compact('categories', 'products', 'mobileGroups', 'searchCategoryId'));
    }

    public function category(Request $request, Category $category)
    {
        // QR Code detection
        if ($request->filled('table')) {
            session(['table_number' => 'Meja ' . $request->table]);
        }

        $categories = Category::select('id', 'name')->get();
        $query = $category->products()
            ->with([
                'category:id,name',
                'ingredients:id,operational_stock',
                'addons' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
            ])
            ->where('is_available', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->latest()->get();
        $mobileGroups = collect([['category' => $category, 'products' => $products]]);
        $searchCategoryId = $category->id;

        return view('pelanggan.store.index', compact('categories', 'products', 'category', 'mobileGroups', 'searchCategoryId'));
    }

    public function product(Product $product)
    {
        $product->loadMissing(['category:id,name', 'ingredients:id,operational_stock', 'addons']);

        return view('pelanggan.store.product', compact('product'));
    }

    private function mobileGroups($categories, $products)
    {
        $productsByCategory = $products->groupBy('category_id');

        return $categories
            ->map(fn ($category) => [
                'category' => $category,
                'products' => $productsByCategory->get($category->id, collect()),
            ])
            ->filter(fn ($group) => $group['products']->isNotEmpty())
            ->values();
    }

    private function searchCategoryId($categories, ?string $search): ?int
    {
        if (!$search) {
            return null;
        }

        $normalizedSearch = Str::lower($search);
        $matchedCategory = $categories
            ->sortByDesc(fn ($category) => Str::length($category->name))
            ->first(
            fn ($category) => Str::contains($normalizedSearch, Str::lower($category->name))
        );

        return $matchedCategory?->id;
    }
}

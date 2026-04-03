<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        // QR Code detection
        if ($request->filled('table')) {
            session(['table_number' => 'Meja ' . $request->table]);
        }

        $categories = Category::all();
        $query = Product::where('is_available', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->latest()->get();
        return view('pelanggan.store.index', compact('categories', 'products'));
    }

    public function category(Request $request, Category $category)
    {
        // QR Code detection
        if ($request->filled('table')) {
            session(['table_number' => 'Meja ' . $request->table]);
        }

        $categories = Category::all();
        $query = $category->products()->where('is_available', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->latest()->get();
        return view('pelanggan.store.index', compact('categories', 'products', 'category'));
    }

    public function product(Product $product)
    {
        return view('pelanggan.store.product', compact('product'));
    }
}

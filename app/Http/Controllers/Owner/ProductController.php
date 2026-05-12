<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->latest()->get();
        return view('owner.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $ingredients = \App\Models\Ingredient::all();
        return view('owner.products.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'ingredient_names' => 'nullable|array',
            'ingredient_names.*' => 'nullable|string|max:255',
            'amounts' => 'nullable|array',
            'amounts.*' => 'nullable|numeric|min:0.01',
            'units' => 'nullable|array',
            'units.*' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_available'] = $request->has('is_available');

        $category = Category::firstOrCreate(['name' => trim($validated['category_name'])]);
        $validated['category_id'] = $category->id;
        unset($validated['category_name']);

        $product = Product::create($validated);

        if ($request->has('ingredient_names')) {
            $ingredients = [];
            foreach ($request->ingredient_names as $index => $name) {
                if (!empty($name) && !empty($request->amounts[$index])) {
                    $unit = $request->units[$index] ?? '';
                    $ingredient = \App\Models\Ingredient::firstOrCreate(
                        ['name' => trim($name)],
                        ['unit' => trim($unit), 'warehouse_stock' => 0, 'operational_stock' => 0]
                    );
                    // Update unit if changed
                    if ($ingredient->unit !== trim($unit)) {
                        $ingredient->update(['unit' => trim($unit)]);
                    }
                    $ingredients[$ingredient->id] = ['amount_needed' => $request->amounts[$index]];
                }
            }
            $product->ingredients()->sync($ingredients);
        }

        return redirect()->route('owner.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $ingredients = \App\Models\Ingredient::all();
        $product->load('ingredients');
        return view('owner.products.edit', compact('product', 'categories', 'ingredients'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'ingredient_names' => 'nullable|array',
            'ingredient_names.*' => 'nullable|string|max:255',
            'amounts' => 'nullable|array',
            'amounts.*' => 'nullable|numeric|min:0.01',
            'units' => 'nullable|array',
            'units.*' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_available'] = $request->has('is_available');

        $category = Category::firstOrCreate(['name' => trim($validated['category_name'])]);
        $validated['category_id'] = $category->id;
        unset($validated['category_name']);

        $product->update($validated);

        $ingredients = [];
        if ($request->has('ingredient_names')) {
            foreach ($request->ingredient_names as $index => $name) {
                if (!empty($name) && !empty($request->amounts[$index])) {
                    $unit = $request->units[$index] ?? '';
                    $ingredient = \App\Models\Ingredient::firstOrCreate(
                        ['name' => trim($name)],
                        ['unit' => trim($unit), 'warehouse_stock' => 0, 'operational_stock' => 0]
                    );
                    if ($ingredient->unit !== trim($unit)) {
                        $ingredient->update(['unit' => trim($unit)]);
                    }
                    $ingredients[$ingredient->id] = ['amount_needed' => $request->amounts[$index]];
                }
            }
        }
        $product->ingredients()->sync($ingredients);

        return redirect()->route('owner.products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return redirect()->route('owner.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

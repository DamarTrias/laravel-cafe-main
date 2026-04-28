<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('owner.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('owner.ingredients.create');
    }

    private function getRedirectRoute()
    {
        return auth()->user()->role . '.ingredients.index';
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'owner') {
            return redirect()->back()->with('error', 'Hanya Owner yang dapat menambahkan bahan baru.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'warehouse_stock' => 'required|numeric|min:0',
            'operational_stock' => 'required|numeric|min:0',
        ]);

        Ingredient::create($validated);

        return redirect()->route($this->getRedirectRoute())->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit(Ingredient $ingredient)
    {
        if (auth()->user()->role !== 'owner') {
            return redirect()->route($this->getRedirectRoute())->with('error', 'Akses ditolak.');
        }
        return view('owner.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        if (auth()->user()->role !== 'owner') {
            return redirect()->back()->with('error', 'Hanya Owner yang dapat mengubah data bahan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'warehouse_stock' => 'required|numeric|min:0',
            'operational_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return redirect()->route($this->getRedirectRoute())->with('success', 'Bahan berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient)
    {
        if (auth()->user()->role !== 'owner') {
            return redirect()->back()->with('error', 'Hanya Owner yang dapat menghapus bahan.');
        }

        $ingredient->delete();
        return redirect()->route($this->getRedirectRoute())->with('success', 'Bahan berhasil dihapus.');
    }

    /**
     * Handle stock transfer from warehouse to operational.
     */
    public function transfer(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $validated['amount'];

        if ($ingredient->warehouse_stock < $amount) {
            return redirect()->back()->with('error', "Stok gudang tidak mencukupi untuk transfer {$amount} {$ingredient->unit}");
        }

        $ingredient->decrement('warehouse_stock', $amount);
        $ingredient->increment('operational_stock', $amount);

        return redirect()->route($this->getRedirectRoute())->with('success', "Berhasil memindahkan {$amount} {$ingredient->unit} ke stok operasional.");
    }
}

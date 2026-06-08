<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        $lowStockIngredients = \App\Models\Ingredient::where('operational_stock', '<', 5)->orderBy('operational_stock')->get();
        return view('admin.orders.index', compact('orders', 'lowStockIngredients'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'items.addons', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,dibatalkan,selesai'
        ]);

        $order->update($validated);
        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function print(Order $order)
    {
        $order->load('items.product', 'items.addons', 'user');
        return view('admin.orders.print', compact('order'));
    }
}

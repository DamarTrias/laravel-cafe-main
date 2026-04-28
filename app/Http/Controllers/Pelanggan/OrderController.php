<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();
        return view('pelanggan.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('items.product');
        return view('pelanggan.orders.show', compact('order'));
    }

    public function status(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        return response()->json([
            'status' => $order->status,
            'status_label' => ucfirst($order->status),
            'status_color' => $order->status // Matches existing CSS classes
        ]);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('pelanggan.store')->with('error', 'Keranjang belanja kosong');
        }

        $validated = $request->validate([
            'note' => 'nullable|string',
            'order_type' => 'required|in:Dine In,Take Away',
            'table_number' => 'required_if:order_type,Dine In|nullable|string',
            'payment_method' => 'required|in:Cash,QRIS'
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $tableNumber = $validated['order_type'] === 'Take Away' ? 'Bawa Pulang' : $validated['table_number'];

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'note' => $validated['note'] ?? null,
                'table_number' => $tableNumber,
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($cart as $id => $item) {
                // Final stock check
                $product = \App\Models\Product::with('ingredients')->find($id);
                if (!$product || $product->max_quantity < $item['quantity']) {
                    throw new \Exception("Stok bahan untuk {$item['name']} tidak mencukupi.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Reduce ingredient operational stock
                foreach ($product->ingredients as $ingredient) {
                    $ingredient->decrement('operational_stock', $ingredient->pivot->amount_needed * $item['quantity']);
                }
            }

            session()->forget('cart');
            DB::commit();

            $message = 'Pesanan berhasil dibuat!';
            if ($order->payment_method === 'QRIS') {
                $message .= ' Silakan unggah bukti transfer Anda di bawah ini.';
                return redirect()->route('pelanggan.orders.show', $order)->with('success', $message);
            }

            return redirect()->route('pelanggan.orders')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        if ($order->status !== 'pending' || $order->payment_method === 'COD' || $order->payment_method === 'Cash') {
            return redirect()->back()->with('error', 'Pesanan tidak valid untuk upload bukti.');
        }
        
        // Let's use QRIS check
        if ($order->payment_method !== 'QRIS' && $order->payment_method !== 'Transfer') {
            return redirect()->back()->with('error', 'Pesanan tidak valid untuk upload bukti.');
        }

        $request->validate([
            'proof_of_transfer' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('proof_of_transfer')) {
            $path = $request->file('proof_of_transfer')->store('proofs', 'public');
            $order->update(['proof_of_transfer' => $path]);
            return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Pilih file terlebih dahulu.');
    }
}

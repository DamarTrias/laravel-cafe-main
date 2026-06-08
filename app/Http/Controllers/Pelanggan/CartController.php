<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartTotal(array $cart): int
    {
        $total = 0;
        foreach ($cart as $item) {
            $unitPrice = $item['unit_price'] ?? $item['price'];
            $total += $unitPrice * $item['quantity'];
        }

        return $total;
    }

    private function cartJsonResponse(array $cart, int|string $productId, string $message)
    {
        $item = $cart[$productId] ?? null;

        return response()->json([
            'success' => true,
            'message' => $message,
            'item_removed' => $item === null,
            'item_id' => (string) $productId,
            'quantity' => $item['quantity'] ?? 0,
            'item_subtotal' => $item ? ($item['unit_price'] ?? $item['price']) * $item['quantity'] : 0,
            'total' => $this->cartTotal($cart),
            'cart_count' => collect($cart)->sum('quantity'),
        ]);
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $total = $this->cartTotal($cart);
        $productIds = collect($cart)
            ->map(fn ($item, $key) => $item['product_id'] ?? (int) $key)
            ->filter()
            ->unique()
            ->values();
        $cartProducts = Product::with(['addons' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return view('pelanggan.cart.index', compact('cart', 'total', 'cartProducts'));
    }

    public function add(Request $request, Product $product)
    {
        $product->loadMissing('ingredients:id,operational_stock', 'addons');

        $cart = session()->get('cart', []);
        $addonIds = collect($request->input('addons', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $selectedAddons = $product->addons()
            ->where('is_active', true)
            ->whereIn('id', $addonIds)
            ->orderBy('id')
            ->get(['id', 'name', 'price']);
        $cartKey = $product->id . ':' . md5($selectedAddons->pluck('id')->implode(','));
        $currentQty = collect($cart)
            ->filter(fn ($item, $key) => ($item['product_id'] ?? (int) $key) === $product->id)
            ->sum('quantity');
        $maxQty = $product->max_quantity;

        if ($currentQty + 1 > $maxQty) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok bahan tidak mencukupi untuk pesanan ini harian ini.',
                ], 422);
            }

            return redirect()->back()->with('error', "Stok bahan tidak mencukupi untuk pesanan ini harian ini.");
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $addonTotal = (int) $selectedAddons->sum('price');
            $cart[$cartKey] = [
                "product_id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "base_price" => (int) $product->price,
                "unit_price" => (int) $product->price + $addonTotal,
                "image" => $product->image,
                "addons" => $selectedAddons
                    ->map(fn ($addon) => [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => (int) $addon->price,
                    ])
                    ->values()
                    ->all(),
            ];
        }

        session()->put('cart', $cart);
        if ($request->expectsJson()) {
            return $this->cartJsonResponse($cart, $cartKey, 'Produk ditambahkan ke keranjang!');
        }

        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function decrement(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        
        $cartKey = (string) $request->input('cart_key', $product->id);

        if (isset($cart[$cartKey])) {
            if ($cart[$cartKey]['quantity'] > 1) {
                $cart[$cartKey]['quantity']--;
            } else {
                unset($cart[$cartKey]);
            }
            session()->put('cart', $cart);
        }

        if ($request->expectsJson()) {
            return $this->cartJsonResponse($cart, $cartKey, 'Kuantitas produk dikurangi');
        }

        return redirect()->back()->with('success', 'Kuantitas produk dikurangi');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if ($request->expectsJson()) {
            return $this->cartJsonResponse($cart ?? [], $id, 'Produk dihapus dari keranjang');
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang');
    }

    public function updateAddons(Request $request)
    {
        $cart = session()->get('cart', []);
        $cartKey = (string) $request->input('cart_key');

        if (!isset($cart[$cartKey])) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        $item = $cart[$cartKey];
        $product = Product::with(['addons' => fn ($query) => $query->where('is_active', true)])
            ->find($item['product_id'] ?? null);

        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $addonIds = collect($request->input('addons', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $selectedAddons = $product->addons
            ->whereIn('id', $addonIds)
            ->sortBy('id')
            ->values();
        $newCartKey = $product->id . ':' . md5($selectedAddons->pluck('id')->implode(','));
        $addonTotal = (int) $selectedAddons->sum('price');

        $updatedItem = array_merge($item, [
            'base_price' => (int) $product->price,
            'unit_price' => (int) $product->price + $addonTotal,
            'addons' => $selectedAddons
                ->map(fn ($addon) => [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => (int) $addon->price,
                ])
                ->all(),
        ]);

        unset($cart[$cartKey]);
        if (isset($cart[$newCartKey])) {
            $cart[$newCartKey]['quantity'] += $updatedItem['quantity'];
        } else {
            $cart[$newCartKey] = $updatedItem;
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Add-on keranjang berhasil diperbarui.');
    }
}

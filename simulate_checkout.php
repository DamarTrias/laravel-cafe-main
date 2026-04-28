<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Ensure we have users
$user = \App\Models\User::where('role', 'pelanggan')->first();
if (!$user) {
    $user = \App\Models\User::factory()->create(['role' => 'pelanggan']);
}

// 2. Auth user
auth()->login($user);

// 3. Create a cart with our test product (id 24)
$cart = [
    24 => [
        'name' => 'Kopi Hitam',
        'quantity' => 1,
        'price' => 10000,
        'image' => null
    ]
];
session()->put('cart', $cart);

// 4. Create request for checkout
$request = new \Illuminate\Http\Request();
$request->merge([
    'order_type' => 'Take Away',
    'payment_method' => 'Cash',
    'note' => 'Test checkout'
]);

// 5. Invoke OrderController
$controller = new \App\Http\Controllers\Pelanggan\OrderController();
$response = $controller->store($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirect()) {
    echo "Redirect: " . $response->getTargetUrl() . "\n";
    $session = session()->all();
    if (isset($session['_flash']['old']) && in_array('error', $session['_flash']['old'])) {
        echo "Flash Error: " . session('error') . "\n";
    }
    if (session()->has('success')) echo "Flash Success: " . session('success') . "\n";
    if (session()->has('error')) echo "Flash Error: " . session('error') . "\n";
}

// 6. Check stock
$ing = \App\Models\Ingredient::find(2); // Kopi gram
echo "New Operational Stock for {$ing->name}: {$ing->operational_stock} (Expected 4 if was 6)\n";

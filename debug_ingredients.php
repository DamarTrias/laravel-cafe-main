<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::with('ingredients')->get();
foreach ($products as $product) {
    echo "Product: " . $product->name . "\n";
    foreach ($product->ingredients as $ingredient) {
        echo "  - Ingredient: " . $ingredient->name . " (Amount: " . $ingredient->pivot->amount_needed . ")\n";
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::with('ingredients')->get();

foreach ($products as $product) {
    if ($product->ingredients->isEmpty()) {
        continue;
    }
    
    echo "- **{$product->name}**:\n";
    foreach ($product->ingredients as $ing) {
        $amount = (float) $ing->pivot->amount_needed;
        echo "  - {$ing->name}: {$amount} {$ing->unit}\n";
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ingredient;

$ingredients = Ingredient::all();

foreach ($ingredients as $ing) {
    // Memberikan angka bulat yang sederhana dan normal di visual dashboard.
    // Misalnya 200 untuk gudang, dan 50 untuk operasional.
    $ing->update([
        'warehouse_stock' => 200,
        'operational_stock' => 50
    ]);
}

echo "Stock has been reset to normal visual numbers (200 / 50).\n";

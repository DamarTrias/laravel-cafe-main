<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ingredient;

$ingredients = Ingredient::all();

foreach ($ingredients as $ing) {
    $unit = strtolower($ing->unit);
    
    $whStock = 0;
    $opStock = 0;

    if ($unit === 'ml') {
        // Susu, Sirup
        if (strpos(strtolower($ing->name), 'susu') !== false) {
            $whStock = 50000; // 50 Liter di gudang
            $opStock = 10000; // 10 Liter di operasional
        } else {
            // Sirup (botolan, 1 botol biasa 750ml, misal 5 botol operasional)
            $whStock = 15000; // 20 botol
            $opStock = 3750;  // 5 botol
        }
    } 
    elseif ($unit === 'gram') {
        // Kopi, Bubuk, Daging
        if (strpos(strtolower($ing->name), 'kopi') !== false) {
            $whStock = 20000; // 20 Kg
            $opStock = 5000;  // 5 Kg
        } elseif (strpos(strtolower($ing->name), 'daging') !== false || strpos(strtolower($ing->name), 'ayam') !== false || strpos(strtolower($ing->name), 'seafood') !== false) {
            $whStock = 15000; // 15 Kg
            $opStock = 3000;  // 3 Kg
        } else {
            // Bubuk matcha/taro, kentang, spaghetti
            $whStock = 10000; // 10 KG
            $opStock = 2000;  // 2 Kg
        }
    } 
    elseif ($unit === 'kg') {
        $whStock = 20; // 20 Kg
        $opStock = 5;  // 5 Kg
    }
    elseif ($unit === 'porsi' || $unit === 'slice' || $unit === 'pcs') {
        // Dimsum, teh leci, roti tawar (slice), pisang, dll
        if ($unit === 'slice') {
            $whStock = 300; // 15 bungkus isi 20 slice
            $opStock = 60;  // 3 bungkus
        } else {
            $whStock = 200; // 200 porsi frozen/stok
            $opStock = 50;  // 50 porsi disiapkan
        }
    } else {
        // Default aman
        $whStock = 200;
        $opStock = 50;
    }

    $ing->update([
        'warehouse_stock' => $whStock,
        'operational_stock' => $opStock
    ]);
    
    echo "Updated {$ing->name} ({$ing->unit}): Gudang={$whStock}, Operasional={$opStock}\n";
}

echo "Stock adjustment complete.\n";

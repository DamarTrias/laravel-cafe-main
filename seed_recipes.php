<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Ingredient;

// 1. Create Base Ingredients
$ingredientsList = [
    ['name' => 'Biji Kopi Espresso', 'unit' => 'gram'],
    ['name' => 'Susu Segar', 'unit' => 'ml'],
    ['name' => 'Sirup Karamel', 'unit' => 'ml'],
    ['name' => 'Sirup Coklat', 'unit' => 'ml'],
    ['name' => 'Bubuk Matcha', 'unit' => 'gram'],
    ['name' => 'Bubuk Taro', 'unit' => 'gram'],
    ['name' => 'Bubuk Red Velvet', 'unit' => 'gram'],
    ['name' => 'Teh Leci & Buah', 'unit' => 'porsi'],
    ['name' => 'Nasi Putih', 'unit' => 'porsi'],
    ['name' => 'Mie Telur', 'unit' => 'porsi'],
    ['name' => 'Daging Ayam', 'unit' => 'gram'],
    ['name' => 'Daging Sapi', 'unit' => 'gram'],
    ['name' => 'Seafood Mix', 'unit' => 'gram'],
    ['name' => 'Pasta Spaghetti', 'unit' => 'gram'],
    ['name' => 'Kentang Beku', 'unit' => 'gram'],
    ['name' => 'Roti Tawar Tebal', 'unit' => 'slice'],
    ['name' => 'Pisang Kepok', 'unit' => 'pcs'],
    ['name' => 'Dimsum Frozen', 'unit' => 'porsi'],
    ['name' => 'Platter Mix Frozen', 'unit' => 'porsi'],
];

$ingMap = [];
foreach ($ingredientsList as $ing) {
    // Beri stok default 1000 agar gampang dites
    $created = Ingredient::firstOrCreate(
        ['name' => $ing['name']], 
        [
            'unit' => $ing['unit'], 
            'warehouse_stock' => 5000, 
            'operational_stock' => 1000
        ]
    );
    $ingMap[$ing['name']] = $created->id;
}

$products = Product::all();

foreach ($products as $product) {
    $recipe = [];
    $name = strtolower($product->name);

    // KOPI
    if ($product->category_id == 1) {
        $recipe[$ingMap['Biji Kopi Espresso']] = ['amount_needed' => 18]; // 18 gram kopi

        if (strpos($name, 'latte') !== false || strpos($name, 'cappuccino') !== false || strpos($name, 'macchiato') !== false) {
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        }
        if (strpos($name, 'caramel') !== false) {
            $recipe[$ingMap['Sirup Karamel']] = ['amount_needed' => 20];
        }
        if (strpos($name, 'mocha') !== false) {
            $recipe[$ingMap['Sirup Coklat']] = ['amount_needed' => 20];
        }
    }

    // NON KOPI
    elseif ($product->category_id == 2) {
        if (strpos($name, 'matcha') !== false) {
            $recipe[$ingMap['Bubuk Matcha']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'taro') !== false) {
            $recipe[$ingMap['Bubuk Taro']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'red velvet') !== false) {
            $recipe[$ingMap['Bubuk Red Velvet']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'chocolate') !== false) {
            $recipe[$ingMap['Sirup Coklat']] = ['amount_needed' => 30];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'lychee') !== false) {
            $recipe[$ingMap['Teh Leci & Buah']] = ['amount_needed' => 1];
        }
    }

    // MAKANAN
    elseif ($product->category_id == 3) {
        if (strpos($name, 'nasi') !== false || strpos($name, 'rice') !== false) {
            $recipe[$ingMap['Nasi Putih']] = ['amount_needed' => 1];
        }
        if (strpos($name, 'mie') !== false) {
            $recipe[$ingMap['Mie Telur']] = ['amount_needed' => 1];
        }
        if (strpos($name, 'spaghetti') !== false) {
            $recipe[$ingMap['Pasta Spaghetti']] = ['amount_needed' => 100];
            $recipe[$ingMap['Daging Sapi']] = ['amount_needed' => 50];
        }
        
        if (strpos($name, 'ayam') !== false || strpos($name, 'chicken') !== false) {
            $recipe[$ingMap['Daging Ayam']] = ['amount_needed' => 150];
        }
        if (strpos($name, 'beef') !== false) {
            $recipe[$ingMap['Daging Sapi']] = ['amount_needed' => 100];
        }
        if (strpos($name, 'seafood') !== false) {
            $recipe[$ingMap['Seafood Mix']] = ['amount_needed' => 100];
        }
    }

    // CEMILAN
    elseif ($product->category_id == 4) {
        if (strpos($name, 'kentang') !== false || strpos($name, 'fries') !== false) {
            $recipe[$ingMap['Kentang Beku']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'platter') !== false) {
            $recipe[$ingMap['Platter Mix Frozen']] = ['amount_needed' => 1];
        } elseif (strpos($name, 'roti') !== false) {
            $recipe[$ingMap['Roti Tawar Tebal']] = ['amount_needed' => 2];
        } elseif (strpos($name, 'pisang') !== false) {
            $recipe[$ingMap['Pisang Kepok']] = ['amount_needed' => 3];
        } elseif (strpos($name, 'dimsum') !== false) {
            $recipe[$ingMap['Dimsum Frozen']] = ['amount_needed' => 1];
        }
    }

    // Jika produknya itu buatan kita tadi (misal Kopi Hitam), biarin aja.
    // Tapi untuk semua produk seeder, kita set.
    if (!empty($recipe)) {
        $product->ingredients()->sync($recipe);
        echo "Set recipe for: " . $product->name . "\n";
    } else {
        echo "Skipped: " . $product->name . " (No exact match or already mapped)\n";
    }
}
echo "Done provisioning recipes.\n";

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Ingredient;

// 1. Create Base Ingredients
$ingredientsList = [
    // Minuman - Bahan Utama & Tambahan
    ['name' => 'Biji Kopi Espresso', 'unit' => 'gram'],
    ['name' => 'Susu Segar', 'unit' => 'ml'],
    ['name' => 'Air Panas', 'unit' => 'ml'],
    ['name' => 'Air Mineral', 'unit' => 'ml'],
    ['name' => 'Gula Cair', 'unit' => 'ml'],
    ['name' => 'Es Batu', 'unit' => 'gram'],
    ['name' => 'Sirup Karamel', 'unit' => 'ml'],
    ['name' => 'Sirup Coklat', 'unit' => 'ml'],
    ['name' => 'Bubuk Matcha', 'unit' => 'gram'],
    ['name' => 'Bubuk Taro', 'unit' => 'gram'],
    ['name' => 'Bubuk Red Velvet', 'unit' => 'gram'],
    ['name' => 'Teh Leci & Buah', 'unit' => 'porsi'],

    // Makanan Berat & Lauk
    ['name' => 'Nasi Putih', 'unit' => 'porsi'],
    ['name' => 'Mie Telur', 'unit' => 'porsi'],
    ['name' => 'Daging Ayam (Fillet)', 'unit' => 'gram'],
    ['name' => 'Daging Sapi (Slice)', 'unit' => 'gram'],
    ['name' => 'Seafood Mix', 'unit' => 'gram'],
    ['name' => 'Pasta Spaghetti', 'unit' => 'gram'],
    ['name' => 'Telur', 'unit' => 'butir'],
    ['name' => 'Smoked Beef', 'unit' => 'slice'],
    ['name' => 'Sayuran Sawi/Kol', 'unit' => 'gram'],

    // Bumbu & Saus Makanan
    ['name' => 'Bumbu Nasi Goreng', 'unit' => 'porsi'],
    ['name' => 'Bumbu Mie Goreng', 'unit' => 'porsi'],
    ['name' => 'Saus Bolognese', 'unit' => 'ml'],
    ['name' => 'Saus Teriyaki', 'unit' => 'ml'],
    ['name' => 'Kecap Manis', 'unit' => 'ml'],
    ['name' => 'Bawang Bombay', 'unit' => 'gram'],
    ['name' => 'Kerupuk', 'unit' => 'porsi'],
    ['name' => 'Saus Sambal', 'unit' => 'ml'],
    ['name' => 'Mayonnaise', 'unit' => 'ml'],

    // Cemilan & Platter
    ['name' => 'Kentang Beku', 'unit' => 'gram'],
    ['name' => 'Sosis', 'unit' => 'pcs'],
    ['name' => 'Chicken Nugget', 'unit' => 'pcs'],
    ['name' => 'Dimsum Frozen', 'unit' => 'porsi'],
    ['name' => 'Roti Tawar Tebal', 'unit' => 'slice'],
    ['name' => 'Pisang Kepok', 'unit' => 'pcs'],
    ['name' => 'Tepung Terigu / Pisang Goreng', 'unit' => 'gram'],
    ['name' => 'Tepung Panir', 'unit' => 'gram'],

    // Topping Manis & Keju
    ['name' => 'Keju Cheddar Parut', 'unit' => 'gram'],
    ['name' => 'Keju Mozzarella', 'unit' => 'gram'],
    ['name' => 'Meses Coklat', 'unit' => 'gram'],
    ['name' => 'Susu Kental Manis', 'unit' => 'ml'],
];

$ingMap = [];
foreach ($ingredientsList as $ing) {
    $created = Ingredient::firstOrCreate(
        ['name' => $ing['name']],
        [
            'unit' => $ing['unit'],
            'warehouse_stock' => 5000,
            'operational_stock' => 1000
        ]
    );
    if ($created->unit !== $ing['unit']) {
        $created->update(['unit' => $ing['unit']]);
    }
    $ingMap[$ing['name']] = $created->id;
}

$products = Product::all();

foreach ($products as $product) {
    $recipe = [];
    $name = strtolower($product->name);
    $isIced = (strpos($name, 'hot') === false);

    // KOPI
    if ($product->category_id == 1) {
        $recipe[$ingMap['Biji Kopi Espresso']] = ['amount_needed' => 18];

        if ($isIced) {
            $recipe[$ingMap['Es Batu']] = ['amount_needed' => 150];
            $recipe[$ingMap['Air Mineral']] = ['amount_needed' => 50];
        } else {
            $recipe[$ingMap['Air Panas']] = ['amount_needed' => 50];
        }

        if (strpos($name, 'latte') !== false || strpos($name, 'cappuccino') !== false || strpos($name, 'macchiato') !== false) {
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
            $recipe[$ingMap['Gula Cair']] = ['amount_needed' => 15];
        }

        if (strpos($name, 'americano') !== false || strpos($name, 'long black') !== false) {
            if ($isIced) {
                $recipe[$ingMap['Air Mineral']] = ['amount_needed' => 150];
            } else {
                $recipe[$ingMap['Air Panas']] = ['amount_needed' => 150];
            }
        }

        if (strpos($name, 'caramel') !== false) {
            $recipe[$ingMap['Sirup Karamel']] = ['amount_needed' => 20];
            unset($recipe[$ingMap['Gula Cair']]);
        }
        if (strpos($name, 'mocha') !== false) {
            $recipe[$ingMap['Sirup Coklat']] = ['amount_needed' => 20];
            unset($recipe[$ingMap['Gula Cair']]);
        }
    }

    // NON KOPI
    elseif ($product->category_id == 2) {
        if ($isIced) {
            $recipe[$ingMap['Es Batu']] = ['amount_needed' => 150];
            $recipe[$ingMap['Air Mineral']] = ['amount_needed' => 50];
        } else {
            $recipe[$ingMap['Air Panas']] = ['amount_needed' => 50];
        }

        if (strpos($name, 'matcha') !== false) {
            $recipe[$ingMap['Bubuk Matcha']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
            $recipe[$ingMap['Gula Cair']] = ['amount_needed' => 15];
        } elseif (strpos($name, 'taro') !== false) {
            $recipe[$ingMap['Bubuk Taro']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
            $recipe[$ingMap['Gula Cair']] = ['amount_needed' => 15];
        } elseif (strpos($name, 'red velvet') !== false) {
            $recipe[$ingMap['Bubuk Red Velvet']] = ['amount_needed' => 25];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
            $recipe[$ingMap['Gula Cair']] = ['amount_needed' => 15];
        } elseif (strpos($name, 'chocolate') !== false) {
            $recipe[$ingMap['Sirup Coklat']] = ['amount_needed' => 30];
            $recipe[$ingMap['Susu Segar']] = ['amount_needed' => 150];
        } elseif (strpos($name, 'lychee') !== false) {
            $recipe[$ingMap['Teh Leci & Buah']] = ['amount_needed' => 1];
            $recipe[$ingMap['Air Mineral']] = ['amount_needed' => 150];
            $recipe[$ingMap['Gula Cair']] = ['amount_needed' => 20];
        }
    }

    // MAKANAN
    elseif ($product->category_id == 3) {
        if (strpos($name, 'nasi') !== false || strpos($name, 'rice') !== false) {
            $recipe[$ingMap['Nasi Putih']] = ['amount_needed' => 1];

            if (strpos($name, 'goreng') !== false) {
                $recipe[$ingMap['Telur']] = ['amount_needed' => 1];
                $recipe[$ingMap['Bumbu Nasi Goreng']] = ['amount_needed' => 1];
                $recipe[$ingMap['Kecap Manis']] = ['amount_needed' => 15];
                $recipe[$ingMap['Kerupuk']] = ['amount_needed' => 1];
            }
            if (strpos($name, 'beef teriyaki') !== false) {
                $recipe[$ingMap['Daging Sapi (Slice)']] = ['amount_needed' => 80];
                $recipe[$ingMap['Saus Teriyaki']] = ['amount_needed' => 30];
                $recipe[$ingMap['Bawang Bombay']] = ['amount_needed' => 15];
            }
        } elseif (strpos($name, 'mie') !== false) {
            $recipe[$ingMap['Mie Telur']] = ['amount_needed' => 1];
            $recipe[$ingMap['Bumbu Mie Goreng']] = ['amount_needed' => 1];
            $recipe[$ingMap['Kecap Manis']] = ['amount_needed' => 15];
            $recipe[$ingMap['Sayuran Sawi/Kol']] = ['amount_needed' => 30];
            $recipe[$ingMap['Telur']] = ['amount_needed' => 1];

            if (strpos($name, 'seafood') !== false) {
                $recipe[$ingMap['Seafood Mix']] = ['amount_needed' => 80];
            }
        } elseif (strpos($name, 'spaghetti') !== false) {
            $recipe[$ingMap['Pasta Spaghetti']] = ['amount_needed' => 100];
            $recipe[$ingMap['Saus Bolognese']] = ['amount_needed' => 80];
            $recipe[$ingMap['Keju Cheddar Parut']] = ['amount_needed' => 15];
        } elseif (strpos($name, 'ayam') !== false || strpos($name, 'chicken') !== false) {
            $recipe[$ingMap['Daging Ayam (Fillet)']] = ['amount_needed' => 150];

            if (strpos($name, 'cordon bleu') !== false) {
                $recipe[$ingMap['Smoked Beef']] = ['amount_needed' => 1];
                $recipe[$ingMap['Keju Mozzarella']] = ['amount_needed' => 20];
                $recipe[$ingMap['Tepung Panir']] = ['amount_needed' => 30];
                $recipe[$ingMap['Kentang Beku']] = ['amount_needed' => 50]; // Side dish kentang
                $recipe[$ingMap['Saus Sambal']] = ['amount_needed' => 20];
            }
        }
    }

    // CEMILAN
    elseif ($product->category_id == 4) {
        if (strpos($name, 'kentang') !== false || strpos($name, 'fries') !== false) {
            $recipe[$ingMap['Kentang Beku']] = ['amount_needed' => 150];
            $recipe[$ingMap['Saus Sambal']] = ['amount_needed' => 20];
        } elseif (strpos($name, 'platter') !== false) {
            $recipe[$ingMap['Kentang Beku']] = ['amount_needed' => 80];
            $recipe[$ingMap['Sosis']] = ['amount_needed' => 2];
            $recipe[$ingMap['Chicken Nugget']] = ['amount_needed' => 4];
            $recipe[$ingMap['Saus Sambal']] = ['amount_needed' => 30];
            $recipe[$ingMap['Mayonnaise']] = ['amount_needed' => 20];
        } elseif (strpos($name, 'roti') !== false) {
            $recipe[$ingMap['Roti Tawar Tebal']] = ['amount_needed' => 2];

            if (strpos($name, 'coklat') !== false || strpos($name, 'keju') !== false) {
                $recipe[$ingMap['Susu Kental Manis']] = ['amount_needed' => 20];
                $recipe[$ingMap['Keju Cheddar Parut']] = ['amount_needed' => 20];
                $recipe[$ingMap['Meses Coklat']] = ['amount_needed' => 20];
            }
        } elseif (strpos($name, 'pisang') !== false) {
            $recipe[$ingMap['Pisang Kepok']] = ['amount_needed' => 3];
            $recipe[$ingMap['Tepung Terigu / Pisang Goreng']] = ['amount_needed' => 50];

            if (strpos($name, 'keju') !== false) {
                $recipe[$ingMap['Susu Kental Manis']] = ['amount_needed' => 20];
                $recipe[$ingMap['Keju Cheddar Parut']] = ['amount_needed' => 20];
            }
        } elseif (strpos($name, 'dimsum') !== false) {
            $recipe[$ingMap['Dimsum Frozen']] = ['amount_needed' => 1];
            if (strpos($name, 'mentai') !== false) {
                $recipe[$ingMap['Mayonnaise']] = ['amount_needed' => 20];
                $recipe[$ingMap['Saus Sambal']] = ['amount_needed' => 10]; // Untuk campuran mentai
            } else {
                $recipe[$ingMap['Saus Sambal']] = ['amount_needed' => 20];
            }
        }
    }

    if (!empty($recipe)) {
        $product->ingredients()->sync($recipe);
        echo "Set recipe for: " . $product->name . "\n";
    } else {
        echo "Skipped: " . $product->name . " (No exact match or already mapped)\n";
    }
}
echo "Done provisioning recipes.\n";

echo "\nMenghitung ulang stok operasional untuk minimal 20 porsi per menu...\n";
$allIngredients = Ingredient::all();
foreach ($allIngredients as $ingredient) {
    $totalNeeded = 0;
    foreach ($ingredient->products as $prod) {
        $amountPerPortion = $prod->pivot->amount_needed;
        $totalNeeded += ($amountPerPortion * 20);
    }
    $operationalStock = $totalNeeded > 0 ? $totalNeeded : 100;
    $warehouseStock = $operationalStock * 5;

    $ingredient->update([
        'operational_stock' => $operationalStock,
        'warehouse_stock' => $warehouseStock
    ]);
    echo "Bahan: " . str_pad($ingredient->name, 25) . " | Stok Baru: {$operationalStock} {$ingredient->unit}\n";
}
echo "Selesai memperbarui stok bahan baku!\n";

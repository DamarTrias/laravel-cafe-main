<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

// 1. Create/Find a test ingredient
$ing = Ingredient::firstOrCreate(['name' => 'Kopi'], ['unit' => 'gram', 'warehouse_stock' => 100, 'operational_stock' => 10]);
echo "Initial Operational Stock: " . $ing->operational_stock . "\n";

// 2. Create/Find a test product
$prod = Product::firstOrCreate(['name' => 'Kopi Hitam'], ['price' => 10000, 'category_id' => 1]);
$prod->ingredients()->sync([$ing->id => ['amount_needed' => 2]]);
echo "Linked Ingredient to Product. Amount needed: 2\n";

// 3. Simulate Order logic
DB::beginTransaction();
try {
    $p = Product::with('ingredients')->find($prod->id);
    foreach ($p->ingredients as $ingredient) {
        echo "Decrementing " . $ingredient->name . " by " . ($ingredient->pivot->amount_needed * 1) . "\n";
        $ingredient->decrement('operational_stock', $ingredient->pivot->amount_needed * 1);
    }
    DB::commit();
    echo "Transaction committed.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

$ing->refresh();
echo "Final Operational Stock: " . $ing->operational_stock . "\n";
if ($ing->operational_stock == 8) {
    echo "SUCCESS: Stock reduced correctly.\n";
} else {
    echo "FAILURE: Stock not reduced.\n";
}

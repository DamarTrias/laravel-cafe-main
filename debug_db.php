<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ingredients = \App\Models\Ingredient::all();
echo "INGREDIENTS:\n";
foreach($ingredients as $ing) {
    echo "ID: {$ing->id}, Name: {$ing->name}, Unit: {$ing->unit}, WhStock: {$ing->warehouse_stock}, OpStock: {$ing->operational_stock}\n";
}

$pis = \Illuminate\Support\Facades\DB::table('product_ingredients')->get();
echo "\nPRODUCT_INGREDIENTS:\n";
foreach($pis as $pi) {
    echo "ID: {$pi->id}, ProductID: {$pi->product_id}, IngredientID: {$pi->ingredient_id}, AmountNeeded: {$pi->amount_needed}\n";
}

$orders = \App\Models\Order::latest()->take(2)->get();
echo "\nLATEST ORDERS:\n";
foreach($orders as $o) {
    echo "OrderID: {$o->id}, Status: {$o->status}, Total: {$o->total_price}\n";
    foreach($o->items as $i) {
        echo "  - Item: ProductID {$i->product_id}, Qty {$i->quantity}\n";
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

$out = [];
$out['ingredients'] = \App\Models\Ingredient::all()->toArray();
$out['product_ingredients'] = DB::table('product_ingredients')->get()->toArray();

file_put_contents('db_dump.json', json_encode($out, JSON_PRETTY_PRINT));

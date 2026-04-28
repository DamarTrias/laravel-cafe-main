<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update semua resep agar hanya menggunakan 1 atau 2 satuan bahan saja.
// Ini agar selaras dengan angka stok '50' yang diminta user.
DB::table('product_ingredients')->update(['amount_needed' => 2]);

echo "All recipes have been updated to require small quantities (2 units).\n";

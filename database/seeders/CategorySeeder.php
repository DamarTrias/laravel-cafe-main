<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kopi', 'description' => 'Berbagai macam minuman kopi khas cafe kita.'],
            ['name' => 'Non Kopi', 'description' => 'Minuman segar tanpa kopi.'],
            ['name' => 'Makanan Utama', 'description' => 'Hidangan utama yang mengenyangkan.'],
            ['name' => 'Cemilan', 'description' => 'Snack dan cemilan ringan untuk menemani nongkrong.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}

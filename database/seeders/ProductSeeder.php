<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Kategori 1: Kopi
            [
                'category_id' => 1,
                'name' => 'Espresso',
                'description' => 'Kopi murni yang diekstraksi kental dengan crema tebal',
                'price' => 18000,
            ],
            [
                'category_id' => 1,
                'name' => 'Americano',
                'description' => 'Espresso dengan tambahan air mineral, cocok untuk pecinta kopi hitam',
                'price' => 20000,
            ],
            [
                'category_id' => 1,
                'name' => 'Cafe Latte',
                'description' => 'Espresso dengan susu segar yang di-steam lembut',
                'price' => 25000,
            ],
            [
                'category_id' => 1,
                'name' => 'Cappuccino',
                'description' => 'Espresso dengan paduan susu dan busa susu tebal di atasnya',
                'price' => 25000,
            ],
            [
                'category_id' => 1,
                'name' => 'Caramel Macchiato',
                'description' => 'Kopi susu dengan tambahan sirup karamel manis dan saus karamel',
                'price' => 28000,
            ],
            [
                'category_id' => 1,
                'name' => 'Mocha Latte',
                'description' => 'Perpaduan sempurna antara kopi, coklat, dan susu',
                'price' => 29000,
            ],

            // Kategori 2: Non Kopi
            [
                'category_id' => 2,
                'name' => 'Matcha Latte',
                'description' => 'Teh hijau Jepang asli yang dicampur dengan susu segar',
                'price' => 28000,
            ],
            [
                'category_id' => 2,
                'name' => 'Taro Latte',
                'description' => 'Minuman rasa ubi ungu manis yang creamy dan lembut',
                'price' => 26000,
            ],
            [
                'category_id' => 2,
                'name' => 'Red Velvet Latte',
                'description' => 'Minuman kue red velvet berbentuk cair dengan rasa manis yang khas',
                'price' => 27000,
            ],
            [
                'category_id' => 2,
                'name' => 'Lychee Tea',
                'description' => 'Teh segar dengan rasa buah leci manis ditambah buah leci asli',
                'price' => 22000,
            ],
            [
                'category_id' => 2,
                'name' => 'Chocolate Hot/Ice',
                'description' => 'Coklat premium kental yang disajikan panas atau dingin',
                'price' => 25000,
            ],

            // Kategori 3: Makanan Utama
            [
                'category_id' => 3,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng bumbu rahasia dengan topping telur mata sapi, ayam, dan sosis',
                'price' => 35000,
            ],
            [
                'category_id' => 3,
                'name' => 'Mie Goreng Seafood',
                'description' => 'Mie goreng kenyal dengan udang, cumi, dan bumbu gurih manis',
                'price' => 38000,
            ],
            [
                'category_id' => 3,
                'name' => 'Chicken Cordon Bleu',
                'description' => 'Dada ayam fillet isi smoked beef dan keju leleh, disajikan dengan kentang & saus',
                'price' => 45000,
            ],
            [
                'category_id' => 3,
                'name' => 'Spaghetti Bolognese',
                'description' => 'Pasta spaghetti dengan saus daging sapi cincang, tomat, dan taburan keju',
                'price' => 32000,
            ],
            [
                'category_id' => 3,
                'name' => 'Rice Bowl Beef Teriyaki',
                'description' => 'Nasi putih pulen dengan irisan daging sapi tumis saus manis gurih teriyaki',
                'price' => 36000,
            ],

            // Kategori 4: Cemilan
            [
                'category_id' => 4,
                'name' => 'Kentang Goreng',
                'description' => 'French fries renyah tabur garam dengan saus sambal dan mayones',
                'price' => 18000,
            ],
            [
                'category_id' => 4,
                'name' => 'Platter Mix',
                'description' => 'Porsi sharing berisi sosis, kentang, chicken nugget, dan onion ring',
                'price' => 35000,
            ],
            [
                'category_id' => 4,
                'name' => 'Roti Bakar Coklat Keju',
                'description' => 'Roti tebal yang dipanggang dengan olesan mentega, taburan meses, dan keju parut berlimpah',
                'price' => 22000,
            ],
            [
                'category_id' => 4,
                'name' => 'Pisang Goreng Keju',
                'description' => 'Pisang manis goreng tepung yang disajikan dengan susu kental manis plus keju',
                'price' => 20000,
            ],
            [
                'category_id' => 4,
                'name' => 'Dimsum Mentai',
                'description' => 'Aneka dimsum ayam udang yang dikukus dengan saus mentai yang di-torch',
                'price' => 25000,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name'], 'category_id' => $product['category_id']],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Owner
        User::create([
            'name' => 'Cafe Owner',
            'email' => 'owner@cafe.com',
            'password' => Hash::make('password'),
            'role' => 'owner'
        ]);

        // Create an Admin/Kasir
        User::create([
            'name' => 'Cafe Admin',
            'email' => 'admin@cafe.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create a Pelanggan
        User::create([
            'name' => 'Customer',
            'email' => 'customer@cafe.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan'
        ]);
        
        // Seed 5 random customers
        User::factory(5)->create(['role' => 'pelanggan']);
    }
}

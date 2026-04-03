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
        User::updateOrCreate(
            ['email' => 'owner@cafe.com'],
            [
                'name' => 'Cafe Owner',
                'password' => Hash::make('password'),
                'role' => 'owner'
            ]
        );

        // Create an Admin/Kasir
        User::updateOrCreate(
            ['email' => 'admin@cafe.com'],
            [
                'name' => 'Cafe Admin',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        // Create a Pelanggan
        User::updateOrCreate(
            ['email' => 'customer@cafe.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'pelanggan'
            ]
        );
        
        // Seed 5 random customers
        User::factory(5)->create(['role' => 'pelanggan']);
    }
}

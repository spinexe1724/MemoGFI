<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void {
    // Akun Staff
    User::create([
        'name' => 'Staff Account', 
        'email' => 'staff@test.com', 
        'password' => bcrypt('password'), 
        'role' => 'staff'
    ]);
    
    // Akun 5 GM dengan email berbasis nama
    $gms = [
        ['name' => 'Tohir Sutanto', 'email' => 'tohir@test.com'],
        ['name' => 'Irwan Susanto', 'email' => 'irwan@test.com'],
        ['name' => 'Kwi Hui Fen', 'email' => 'kwi@test.com'],
        ['name' => 'Riko Aryanto', 'email' => 'riko@test.com'],
        ['name' => 'Sastra Hamidjaja', 'email' => 'sastra@test.com'],
    ];

    foreach ($gms as $gm) {
        User::create([
            'name' => $gm['name'],
            'email' => $gm['email'],
            'password' => bcrypt('password'),
            'role' => 'gm'
        ]);
    }
   \App\Models\User::create([
        'name' => 'Super Administrator',
        'email' => 'admin@gratama.com',
        'password' => Hash::make('password123'),
        'role' => 'superadmin',
    ]);
}

}
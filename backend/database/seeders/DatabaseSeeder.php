<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo một tài khoản Admin/Thủ thư để test
        User::create([
            'name' => 'admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password123'), 
            'role' => 'admin', 
        ]);
        
        // Tạo thêm một độc giả
        User::create([
            'name' => 'user',
            'email' => 'user@library.com',
            'password' => Hash::make('password123'),
        ]);
    }
}

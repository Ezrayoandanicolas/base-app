<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(['username' => 'admin', 'name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => Hash::make('admin')]);
        $user->assignRole('admin');
    }
}

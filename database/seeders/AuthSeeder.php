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
        $user = User::create(['username' => 'ezrayoandanicolas', 'name' => 'Ezra Yoanda Nicolas', 'email' => 'ezrayoandanicolas@gmail.com', 'password' => Hash::make('kawachi123')]);
        $user->assignRole('member');
    }
}

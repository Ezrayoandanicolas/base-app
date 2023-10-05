<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RolePermissionSeeder
        $this->call(RolePermissionSeeder::class);
        // Call AuthSeeder
        $this->call(AuthSeeder::class);
        // Call ArticleSeeder
        $this->call(ArticleSeeder::class);
    }
}

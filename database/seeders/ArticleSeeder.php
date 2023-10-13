<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Article::create(['title' => 'Title 1 Article', 'slug' => Str::slug(date('Y-m-d').'-'.'Title 1 Article'), 'content' => 'Hello Ges ini article Pertama Saya', 'image' => '121241234.png', 'user_id' => 1]);
    }
}

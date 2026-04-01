<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posts;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        Posts::factory()->count(50)->create();
    }
}
<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostsFactory extends Factory
{
    public function definition(): array
    {
        $filePath = fake()->image(
            storage_path('app/public/posts'),
            640,
            480
        );

        $fileName = basename($filePath);

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'category_id' => Category::inRandomOrder()->value('id'),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'content_media' => 'posts/' . $fileName,
            'published_at' => fake()->dateTime(),
            'edited_at' => fake()->dateTime(),
        ];
    }
}
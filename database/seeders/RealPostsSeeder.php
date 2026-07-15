<?php

namespace Database\Seeders;

use App\Models\Posts;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RealPostsSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'The Future of Artificial Intelligence',
                'category' => 'Electronics',
                'content' => 'Artificial Intelligence is rapidly evolving, transforming industries from healthcare to finance. In this post, we explore the latest trends and what the future holds for AI technology.',
                'image' => 'posts/img-01.jpg'
            ],
            [
                'title' => '10 Must-Visit Destinations in Europe',
                'category' => 'Travel',
                'content' => 'Europe offers a rich tapestry of cultures, history, and stunning landscapes. From the romantic streets of Paris to the ancient ruins of Athens, here are 10 places you must visit.',
                'image' => 'posts/img-02.jpg'
            ],
            [
                'title' => 'Healthy Eating: A Beginner\'s Guide',
                'category' => 'Health & Wellness',
                'content' => 'Starting a healthy diet can be overwhelming. We break down the basics of nutrition and provide simple tips to help you eat better and feel great.',
                'image' => 'posts/img-03.jpg'
            ],
            [
                'title' => 'The Rise of Remote Work',
                'category' => 'Office Supplies',
                'content' => 'Remote work has become the new norm for many. We discuss the benefits, challenges, and how to stay productive while working from home.',
                'image' => 'posts/img-04.jpg'
            ],
            [
                'title' => 'Photography Tips for Beginners',
                'category' => 'Photography',
                'content' => 'Capture stunning photos with these essential photography tips. Learn about lighting, composition, and how to make the most of your camera.',
                'image' => 'posts/img-05.jpg'
            ],
            [
                'title' => 'Sustainable Living: Easy Steps',
                'category' => 'Sustainable Products',
                'content' => 'Living sustainably doesn\'t have to be difficult. Discover small changes you can make in your daily life to reduce your environmental impact.',
                'image' => 'posts/img-06.jpg'
            ],
            [
                'title' => 'The History of Classical Music',
                'category' => 'Music',
                'content' => 'Dive into the rich history of classical music. We explore the lives and works of great composers like Bach, Mozart, and Beethoven.',
                'image' => 'posts/img-07.jpg'
            ],
            [
                'title' => 'Exploring the Wonders of Space',
                'category' => 'Outdoor & Adventure',
                'content' => 'Space exploration continues to push the boundaries of human knowledge. Learn about recent missions and the mysteries of the cosmos.',
                'image' => 'posts/img-08.jpg'
            ],
            [
                'title' => 'The Best Tech Gadgets of 2026',
                'category' => 'Tech Accessories',
                'content' => 'Stay ahead of the curve with our roundup of the best tech gadgets of 2026. From smartwatches to foldable phones, we cover it all.',
                'image' => 'posts/img-09.jpg'
            ],
            [
                'title' => 'Home Decor Trends for the New Year',
                'category' => 'Home & Kitchen',
                'content' => 'Refresh your living space with the latest home decor trends. Discover new styles, colors, and textures that will breathe life into your home.',
                'image' => 'posts/img-10.jpg'
            ],
            [
                'title' => 'Gardening for Urban Spaces',
                'category' => 'Garden',
                'content' => 'You don\'t need a big backyard to enjoy gardening. Learn how to create a beautiful and productive garden in small urban spaces.',
                'image' => 'posts/img-11.jpg'
            ],
            [
                'title' => 'The Evolution of Video Games',
                'category' => 'Video Games',
                'content' => 'Video games have come a long way since the days of Pong. We trace the evolution of gaming from 8-bit classics to immersive VR experiences.',
                'image' => 'posts/img-12.jpg'
            ],
            [
                'title' => 'Mindfulness and Meditation Benefits',
                'category' => 'Health',
                'content' => 'In a fast-paced world, mindfulness and meditation can help you find peace. Explore the science-backed benefits of these ancient practices.',
                'image' => 'posts/img-13.jpg'
            ],
            [
                'title' => 'The Art of Coffee Brewing',
                'category' => 'Food & Beverages',
                'content' => 'Become your own barista with our guide to coffee brewing. Learn about different methods, from pour-over to espresso, and how to get the perfect cup.',
                'image' => 'posts/img-14.jpg'
            ],
            [
                'title' => 'Top 5 Hiking Trails in North America',
                'category' => 'Outdoor Gear',
                'content' => 'Lace up your boots and hit the trails. We\'ve curated a list of the top 5 hiking destinations in North America for every skill level.',
                'image' => 'posts/img-15.jpg'
            ],
            [
                'title' => 'Modern Architecture: A Visual Journey',
                'category' => 'Home Improvement',
                'content' => 'Explore the bold designs and innovative materials of modern architecture. We take a look at some of the world\'s most iconic contemporary buildings.',
                'image' => 'posts/img-16.jpg'
            ],
            [
                'title' => 'The Impact of Social Media on Society',
                'category' => 'Hobbies',
                'content' => 'Social media has fundamentally changed how we communicate. We examine the positive and negative impacts of these platforms on our daily lives.',
                'image' => 'posts/img-17.jpg'
            ],
            [
                'title' => 'DIY Home Improvement Projects',
                'category' => 'Home Improvement',
                'content' => 'Save money and personalize your home with these DIY projects. From painting walls to building shelves, we provide step-by-step instructions.',
                'image' => 'posts/img-18.jpg'
            ],
            [
                'title' => 'Essential Travel Gear for Every Trip',
                'category' => 'Travel',
                'content' => 'Don\'t leave home without these travel essentials. We\'ve compiled a list of gear that will make your trips more comfortable and organized.',
                'image' => 'posts/img-19.jpg'
            ],
            [
                'title' => 'Understanding Blockchain Technology',
                'category' => 'Electronics',
                'content' => 'Blockchain is more than just cryptocurrency. Learn about the underlying technology and its potential applications in various industries.',
                'image' => 'posts/img-20.jpg'
            ],
            [
                'title' => 'The Joy of Baking: Simple Recipes',
                'category' => 'Grocery',
                'content' => 'There\'s nothing like the smell of fresh bread or cookies. We share simple and delicious baking recipes that anyone can master.',
                'image' => 'posts/img-21.jpg'
            ],
            [
                'title' => 'Vintage Fashion: How to Style It',
                'category' => 'Clothing',
                'content' => 'Vintage fashion is making a comeback. Learn how to incorporate unique pieces into your modern wardrobe for a one-of-a-kind look.',
                'image' => 'posts/img-22.jpg'
            ],
            [
                'title' => 'The Benefits of Regular Exercise',
                'category' => 'Fitness',
                'content' => 'Regular physical activity is vital for your health. Discover the many benefits of exercise, from improved mood to increased energy levels.',
                'image' => 'posts/img-23.jpg'
            ],
            [
                'title' => 'Space Exploration: Next Frontiers',
                'category' => 'Collectibles',
                'content' => 'As we look beyond Earth, what are the next frontiers in space exploration? We discuss upcoming missions to Mars and beyond.',
                'image' => 'posts/img-24.jpg'
            ],
            [
                'title' => 'Digital Minimalism: Finding Focus',
                'category' => 'Smart Home',
                'content' => 'In an age of constant distractions, digital minimalism can help you regain focus. Learn how to declutter your digital life and prioritize what matters.',
                'image' => 'posts/img-25.jpg'
            ],
        ];

        $user = User::first() ?? User::factory()->create();

        foreach ($posts as $postData) {
            $category = Category::where('name', $postData['category'])->first();
            
            Posts::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'content_media' => $postData['image'],
                'category_id' => $category ? $category->id : Category::inRandomOrder()->first()->id,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'edited_at' => Carbon::now(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'login' => 'admin',
            'email' => 'admin@example.com',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        User::factory()->count(10)->create();

        $this->call([
            CategorySeeder::class,
            RealPostsSeeder::class,
        ]);
    }
}

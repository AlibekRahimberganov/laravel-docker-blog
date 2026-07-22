<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FullApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_user_can_register_with_strong_password()
    {
        $response = $this->post('/register', [
            'login' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_create_post()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $response = $this->actingAs($user)->post('/create', [
            'title' => 'My Awesome Post',
            'content' => 'This is the content of my awesome post with at least ten characters.',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('posts', ['title' => 'My Awesome Post']);
    }

    public function test_user_can_like_and_dislike_posts()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = Posts::create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'content' => 'Content for testing reactions.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $batcher = app(\App\Services\ReactionBatchService::class);

        // Like the post
        $this->actingAs($user)->post("/post/{$post->id}/react/like");
        $batcher->flushAll();
        $this->assertEquals(1, $post->fresh()->likes_count);

        // Dislike the post (should remove like)
        $this->actingAs($user)->post("/post/{$post->id}/react/dislike");
        $batcher->flushAll();
        $this->assertEquals(0, $post->fresh()->likes_count);
        $this->assertEquals(1, $post->fresh()->dislikes_count);

        // Recommend (independent)
        $this->actingAs($user)->post("/post/{$post->id}/react/recommend");
        $batcher->flushAll();
        $this->assertEquals(1, $post->fresh()->recommends_count);
        $this->assertEquals(1, $post->fresh()->dislikes_count);
    }

    public function test_post_view_is_tracked()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = Posts::create([
            'user_id' => $user->id,
            'title' => 'Test View Post',
            'content' => 'Content for testing views.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $this->get("/post/{$post->id}");
        
        $this->assertEquals(1, $post->fresh()->views_count);
        $this->assertDatabaseHas('post_views', ['post_id' => $post->id]);
    }

    public function test_statistics_page_and_csv_export()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        Posts::create([
            'user_id' => $user->id,
            'title' => 'My Stats Post',
            'content' => 'Content for testing stats.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/statistics');
        $response->assertStatus(200);
        $response->assertSee('My Stats Post');

        $exportResponse = $this->actingAs($user)->get('/statistics/export');
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('Content-Disposition', 'attachment; filename=post_statistics.csv');
    }

    public function test_unauthorized_user_cannot_delete_post()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = Posts::create([
            'user_id' => $owner->id,
            'title' => 'Owner Post',
            'content' => 'Owner content.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $response = $this->actingAs($otherUser)->delete("/post/{$post->id}");
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}

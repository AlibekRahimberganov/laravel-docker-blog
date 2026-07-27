<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_change_a_users_role()
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->login}/role", ['role' => 'admin']);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_admin_cannot_change_their_own_role()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->patch("/admin/users/{$admin->login}/role", ['role' => 'user']);

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_cannot_delete_their_own_account()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete("/admin/users/{$admin->login}");

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_any_users_post()
    {
        $admin = $this->admin();
        $author = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = Posts::create([
            'user_id' => $author->id,
            'title' => 'Someone else\'s post',
            'content' => 'Content that will be moderated away.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete("/admin/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_manage_categories()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Science',
            'description' => 'Science posts',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Science']);

        $category = Category::where('name', 'Science')->first();

        $this->actingAs($admin)->delete("/admin/categories/{$category->id}")->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_posts()
    {
        $admin = $this->admin();
        $author = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        Posts::create([
            'user_id' => $author->id,
            'title' => 'A protected post',
            'content' => 'This post keeps the category alive.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);

        $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}

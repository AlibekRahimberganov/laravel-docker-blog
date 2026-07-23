<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoAuthorTest extends TestCase
{
    use RefreshDatabase;

    private function createPost(User $owner, Category $category): Posts
    {
        return Posts::create([
            'user_id' => $owner->id,
            'title' => 'Owner Post',
            'content' => 'Owner content long enough to pass validation.',
            'category_id' => $category->id,
            'published_at' => now(),
            'edited_at' => now(),
        ]);
    }

    public function test_creating_post_with_free_text_co_author_persists_row_without_user_id()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $response = $this->actingAs($user)->post('/create', [
            'title' => 'Post With Free Text Co-author',
            'content' => 'This is the content of my awesome post with enough characters.',
            'category_id' => $category->id,
            'co_author_name' => ['Jane Doe'],
        ]);

        $response->assertRedirect('/');
        $post = Posts::where('title', 'Post With Free Text Co-author')->firstOrFail();
        $this->assertDatabaseHas('post_authors', [
            'post_id' => $post->id,
            'user_id' => null,
            'name' => 'Jane Doe',
        ]);
    }

    public function test_creating_post_with_linked_co_author_persists_row_with_user_id()
    {
        $user = User::factory()->create();
        $coAuthor = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $response = $this->actingAs($user)->post('/create', [
            'title' => 'Post With Linked Co-author',
            'content' => 'This is the content of my awesome post with enough characters.',
            'category_id' => $category->id,
            'co_author_user' => [$coAuthor->id],
        ]);

        $response->assertRedirect('/');
        $post = Posts::where('title', 'Post With Linked Co-author')->firstOrFail();
        $this->assertDatabaseHas('post_authors', [
            'post_id' => $post->id,
            'user_id' => $coAuthor->id,
            'name' => null,
        ]);
    }

    public function test_linked_co_author_can_edit_and_delete_the_post()
    {
        $owner = User::factory()->create();
        $coAuthor = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = $this->createPost($owner, $category);
        $post->coAuthors()->create(['user_id' => $coAuthor->id, 'display_order' => 0]);

        // Mirrors the real edit form, which always resubmits the current co-author
        // list alongside the edited fields (a bare PUT without it would wipe co-authors).
        $updateResponse = $this->actingAs($coAuthor)->put("/edit/{$post->id}", [
            'title' => 'Updated By Co-author',
            'content' => 'Updated content long enough to pass validation.',
            'co_author_user' => [$coAuthor->id],
        ]);
        $updateResponse->assertRedirect('/');
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated By Co-author']);

        $deleteResponse = $this->actingAs($coAuthor)->delete("/post/{$post->id}");
        $deleteResponse->assertRedirect('/');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_non_author_non_co_author_still_gets_403()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = $this->createPost($owner, $category);

        $response = $this->actingAs($otherUser)->delete("/post/{$post->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_author_profile_and_own_profile_list_co_authored_posts()
    {
        $owner = User::factory()->create();
        $coAuthor = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);
        $post = $this->createPost($owner, $category);
        $post->coAuthors()->create(['user_id' => $coAuthor->id, 'display_order' => 0]);

        $ownProfile = $this->actingAs($coAuthor)->get('/profile');
        $ownProfile->assertStatus(200);
        $ownProfile->assertSee('Owner Post');

        $publicProfile = $this->get("/author/{$coAuthor->login}");
        $publicProfile->assertStatus(200);
        $publicProfile->assertSee('Owner Post');
    }

    public function test_author_profile_route_is_public_and_404s_for_missing_user()
    {
        $response = $this->get('/author/999999');

        $response->assertStatus(404);
    }
}

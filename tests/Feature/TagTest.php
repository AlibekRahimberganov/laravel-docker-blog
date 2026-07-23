<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Posts;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_post_with_tags_creates_and_attaches_tags()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $response = $this->actingAs($user)->post('/create', [
            'title' => 'Tagged Post',
            'content' => 'This is the content of my tagged post with enough characters.',
            'category_id' => $category->id,
            'tags' => 'Music, Classical',
        ]);

        $response->assertRedirect('/');
        $post = Posts::where('title', 'Tagged Post')->firstOrFail();
        $this->assertEquals(2, $post->tags()->count());
        $this->assertDatabaseHas('tags', ['slug' => 'music']);
        $this->assertDatabaseHas('tags', ['slug' => 'classical']);
    }

    public function test_reusing_a_tag_with_different_casing_does_not_create_a_duplicate()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $this->actingAs($user)->post('/create', [
            'title' => 'First Tagged Post',
            'content' => 'This is the content of my tagged post with enough characters.',
            'category_id' => $category->id,
            'tags' => 'Music',
        ]);

        $this->actingAs($user)->post('/create', [
            'title' => 'Second Tagged Post',
            'content' => 'This is the content of my second tagged post with enough characters.',
            'category_id' => $category->id,
            'tags' => 'music',
        ]);

        $this->assertEquals(1, Tag::count());
    }

    public function test_tag_page_lists_only_posts_with_that_tag()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $this->actingAs($user)->post('/create', [
            'title' => 'Music Post',
            'content' => 'This is the content of my tagged post with enough characters.',
            'category_id' => $category->id,
            'tags' => 'music',
        ]);
        $this->actingAs($user)->post('/create', [
            'title' => 'Untagged Post',
            'content' => 'This is the content of an untagged post with enough characters.',
            'category_id' => $category->id,
        ]);

        $tag = Tag::where('slug', 'music')->firstOrFail();
        $response = $this->get("/tag/{$tag->slug}");

        $response->assertStatus(200);
        $response->assertSee('Music Post');
        $response->assertDontSee('Untagged Post');
    }

    public function test_too_many_tags_is_rejected()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Technology']);

        $response = $this->actingAs($user)->post('/create', [
            'title' => 'Over Tagged Post',
            'content' => 'This is the content of my tagged post with enough characters.',
            'category_id' => $category->id,
            'tags' => implode(',', range(1, 11)),
        ]);

        $response->assertSessionHasErrors('tags');
        $this->assertDatabaseMissing('posts', ['title' => 'Over Tagged Post']);
    }
}

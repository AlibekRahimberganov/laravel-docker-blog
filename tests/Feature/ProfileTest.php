<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_bio_and_avatar()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'bio' => 'Hello, I write about Laravel.',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('blog.profile'));
        $user->refresh();
        $this->assertEquals('Hello, I write about Laravel.', $user->bio);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_updating_bio_without_a_new_avatar_keeps_existing_avatar()
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/existing.jpg']);

        $this->actingAs($user)->put('/profile', ['bio' => 'Updated bio only.']);

        $user->refresh();
        $this->assertEquals('avatars/existing.jpg', $user->avatar);
        $this->assertEquals('Updated bio only.', $user->bio);
    }

    public function test_profile_edit_page_requires_authentication()
    {
        $response = $this->get('/profile/edit');

        $response->assertRedirect('/login');
    }

    public function test_user_can_remove_their_avatar()
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/existing.jpg', 'fake-contents');
        $user = User::factory()->create(['avatar' => 'avatars/existing.jpg']);

        $this->actingAs($user)->put('/profile', ['remove_avatar' => '1']);

        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/existing.jpg');
    }

    public function test_user_without_avatar_gets_default_avatar_url()
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertStringContainsString('default-avatar.svg', $user->avatar_url);
    }
}

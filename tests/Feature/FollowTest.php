<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        $response = $this->actingAs($follower)->post("/follow/{$followed->login}");

        $response->assertRedirect();
        $this->assertTrue($follower->fresh()->isFollowing($followed));
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
        ]);
    }

    public function test_following_is_one_directional()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($a)->post("/follow/{$b->login}");

        $this->assertTrue($a->fresh()->isFollowing($b));
        $this->assertFalse($b->fresh()->isFollowing($a));
    }

    public function test_user_cannot_follow_themselves()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/follow/{$user->login}");

        $response->assertStatus(403);
    }

    public function test_following_the_same_user_twice_does_not_duplicate()
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        $this->actingAs($follower)->post("/follow/{$followed->login}");
        $this->actingAs($follower)->post("/follow/{$followed->login}");

        $this->assertEquals(1, $follower->following()->count());
    }

    public function test_user_can_unfollow()
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();
        $follower->following()->attach($followed->id);

        $response = $this->actingAs($follower)->delete("/follow/{$followed->login}");

        $response->assertRedirect();
        $this->assertFalse($follower->fresh()->isFollowing($followed));
    }

    public function test_followers_and_following_counts()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();

        $a->following()->attach([$b->id, $c->id]);

        $this->assertEquals(2, $a->following()->count());
        $this->assertEquals(1, $b->followers()->count());
        $this->assertEquals(1, $c->followers()->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_a_friend_request()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this->actingAs($sender)->post("/friends/{$receiver->login}");

        $response->assertRedirect();
        $this->assertDatabaseHas('friendships', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_send_duplicate_request()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        Friendship::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);

        $this->actingAs($sender)->post("/friends/{$receiver->login}");

        $this->assertEquals(1, Friendship::count());
    }

    public function test_user_cannot_friend_themselves()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/friends/{$user->login}");

        $response->assertStatus(403);
    }

    public function test_receiver_can_accept_request()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $friendship = Friendship::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($receiver)->put("/friends/{$friendship->id}/accept");

        $response->assertRedirect();
        $this->assertEquals('accepted', $friendship->fresh()->status);
        $this->assertTrue($sender->fresh()->isFriendsWith($receiver));
    }

    public function test_sender_cannot_accept_their_own_request()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $friendship = Friendship::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($sender)->put("/friends/{$friendship->id}/accept");

        $response->assertStatus(403);
    }

    public function test_receiver_can_reject_request()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $friendship = Friendship::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);

        $response = $this->actingAs($receiver)->delete("/friends/{$friendship->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('friendships', ['id' => $friendship->id]);
    }

    public function test_unrelated_user_cannot_accept_or_remove_a_friendship()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $stranger = User::factory()->create();
        $friendship = Friendship::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);

        $this->actingAs($stranger)->put("/friends/{$friendship->id}/accept")->assertStatus(403);
        $this->actingAs($stranger)->delete("/friends/{$friendship->id}")->assertStatus(403);
    }

    public function test_friends_list_includes_both_directions()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        Friendship::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'status' => 'accepted']);

        $this->assertTrue($a->friends()->get()->contains('id', $b->id));
        $this->assertTrue($b->friends()->get()->contains('id', $a->id));
    }
}

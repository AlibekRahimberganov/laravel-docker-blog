<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private function makeFriends(User $a, User $b): void
    {
        Friendship::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'status' => 'accepted']);
    }

    public function test_friends_can_message_each_other()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeFriends($a, $b);

        $response = $this->actingAs($a)->post("/messages/{$b->login}", ['body' => 'Hey there!']);

        $response->assertRedirect(route('messages.show', $b));
        $this->assertDatabaseHas('messages', [
            'sender_id' => $a->id,
            'receiver_id' => $b->id,
            'body' => 'Hey there!',
        ]);
    }

    public function test_non_friends_cannot_message_each_other()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $response = $this->actingAs($a)->post("/messages/{$b->login}", ['body' => 'Hey there!']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('messages', ['body' => 'Hey there!']);
    }

    public function test_viewing_a_thread_marks_messages_as_read()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeFriends($a, $b);
        $message = Message::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'body' => 'Hi']);

        $this->assertNull($message->fresh()->read_at);

        $this->actingAs($b)->get("/messages/{$a->login}");

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_conversation_thread_only_shows_messages_between_the_two_users()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();
        $this->makeFriends($a, $b);
        $this->makeFriends($a, $c);
        Message::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'body' => 'To B']);
        Message::create(['sender_id' => $a->id, 'receiver_id' => $c->id, 'body' => 'To C']);

        $response = $this->actingAs($a)->get("/messages/{$b->login}");

        $response->assertSee('To B');
        $response->assertDontSee('To C');
    }

    public function test_unread_count_reflects_unread_messages()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeFriends($a, $b);
        Message::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'body' => 'One']);
        Message::create(['sender_id' => $a->id, 'receiver_id' => $b->id, 'body' => 'Two']);

        $this->assertEquals(2, $b->unreadMessagesCount());
    }

    public function test_ajax_send_returns_rendered_bubble_html()
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeFriends($a, $b);

        $response = $this->actingAs($a)
            ->postJson("/messages/{$b->login}", ['body' => 'Hey there!']);

        $response->assertOk();
        $response->assertJsonStructure(['html']);
        $this->assertStringContainsString('Hey there!', $response->json('html'));
    }
}

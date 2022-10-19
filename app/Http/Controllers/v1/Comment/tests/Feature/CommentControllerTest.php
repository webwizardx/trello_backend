<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Lists;
use App\Models\Todo;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_comments()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();
        Comment::factory()->count(3)->for($user)->for($todo)->create();

        $response = $this->actingAs($user)->getJson("/api/v1/todos/{$todo->id}/comments");

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_comment()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();
        $commentMessage = 'Comment message';
        $comment = Comment::factory()->for($user)->for($todo)->create(['message' => $commentMessage]);

        $response = $this->actingAs($user)->getJson("/api/v1/comments/{$comment->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.message', $commentMessage);
    }

    public function test_create_comment()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();
        $data = [
            'message' => 'Comment message',
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/comments', $data);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.message', $data['message'])
                    ->where('data.todo_id', $data['todo_id'])
                    ->where('data.user_id', $data['user_id'])
                    ->etc()
            );
    }

    public function test_update_comment()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();
        $data = [
            'message' => 'Comment message updated',
        ];
        $comment = Comment::factory()->for($user)->for($todo)->create();

        $response = $this->actingAs($user)->patchJson("/api/v1/comments/{$comment->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.message', $data['message']);
    }

    public function test_delete_comment()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();
        $comment = Comment::factory()->for($user)->for($todo)->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($comment);
    }
}

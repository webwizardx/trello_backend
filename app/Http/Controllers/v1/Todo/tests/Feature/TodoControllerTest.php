<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Lists;
use App\Models\Todo;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TodoController extends TestCase
{
    use RefreshDatabase;

    public function test_get_todos()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        Todo::factory()->count(3)->hasAttached($user)->for($lists, 'list')->create();

        $response = $this->actingAs($user)->getJson("/api/v1/lists/{$lists->id}/todos");

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_todo()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todoTitle = 'Test Todo title';
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create(['title' => $todoTitle]);

        $response = $this->actingAs($user)->getJson("/api/v1/todos/{$todo->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', $todoTitle);
    }

    public function test_create_todo()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $data = [
            'title' => 'Test Todo title',
            'description' => 'Description',
            'list_id' => $lists->id
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/todos', $data);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $data['title'])
                    ->where('data.description', $data['description'])
                    ->where('data.list_id', $data['list_id'])
                    ->etc()
            );
    }

    public function test_update_todo()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $data = [
            'title' => 'Test Todo title updated'
        ];
        $todo = Todo::factory()->for($lists, 'list')->create();

        $response = $this->actingAs($user)->patchJson("/api/v1/todos/{$todo->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJsonPath(
                'data.title',
                $data['title']
            );
    }

    public function test_delete_todo()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $lists = Lists::factory()->for($board)->create();
        $todo = Todo::factory()->hasAttached($user)->for($lists, 'list')->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/todos/{$todo->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($todo);
    }
}

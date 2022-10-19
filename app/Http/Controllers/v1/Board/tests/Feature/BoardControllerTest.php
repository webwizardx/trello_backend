<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_boards()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        Board::factory()->count(3)->hasAttached($user)->for($workspace)->create();

        $response = $this->actingAs($user)->getJson('/api/v1/boards');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }


    public function test_get_board()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $boardTitle = 'Test board title';
        $board = Board::factory()->hasAttached($user)->for($workspace)->create(['title' => $boardTitle]);

        $response = $this->actingAs($user)->getJson("/api/v1/boards/{$board->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', $boardTitle);
    }

    public function test_create_board()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $data = ['title' => 'Test board title', 'workspace_id' => $workspace->id];

        $response = $this->actingAs($user)->postJson('/api/v1/boards', $data);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $data['title'])
                    ->where('data.workspace_id', $data['workspace_id'])
                    ->etc()
            );
    }

    public function test_update_board()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();
        $data = ['title' => 'Test board title'];

        $response = $this->actingAs($user)->patchJson("/api/v1/boards/{$board->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', $data['title']);
    }

    public function test_delete_board()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->for($workspace)->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/boards/{$board->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($board);
    }
}

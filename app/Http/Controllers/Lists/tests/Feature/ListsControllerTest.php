<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Lists;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_lists()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->create(['workspace_id' => $workspace->id]);
        Lists::factory()->count(3)->create(['board_id' => $board->id]);

        $response = $this->actingAs($user)->getJson("/api/boards/{$board->id}/lists");

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_list()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->create(['workspace_id' => $workspace->id]);
        $listTitle = 'Test List title';
        $lists = Lists::factory()->create(['title' => $listTitle, 'board_id' => $board->id]);

        $response = $this->actingAs($user)->getJson("/api/lists/{$lists->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', $listTitle);
    }

    public function test_create_list()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->create(['workspace_id' => $workspace->id]);
        $data = ['title' => 'Test List title', 'board_id' => $board->id];

        $response = $this->actingAs($user)->postJson('/api/lists/', $data);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $data['title'])
                    ->where('data.board_id', $data['board_id'])
                    ->etc()
            );
    }

    public function test_update_list()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->create(['workspace_id' => $workspace->id]);
        $data = ['title' => 'Test List title updated'];
        $lists = Lists::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($user)->patchJson("/api/lists/{$lists->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJsonPath(
                'data.title',
                $data['title']
            );
    }

    public function test_delete_list()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $board = Board::factory()->hasAttached($user)->create(['workspace_id' => $workspace->id]);
        $lists = Lists::factory()->create(['board_id' => $board->id]);

        $response = $this->actingAs($user)->deleteJson("/api/lists/{$lists->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($lists);
    }
}

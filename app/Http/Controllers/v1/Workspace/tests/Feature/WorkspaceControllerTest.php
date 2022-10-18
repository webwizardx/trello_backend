<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class WorkspaceController extends TestCase
{
    use RefreshDatabase;

    public function test_get_workspaces()
    {
        $user = User::factory()->create();
        Workspace::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/v1/workspaces');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_workspace()
    {
        $user = User::factory()->create();
        $workspaceTitle = 'Testing Workspace';
        $workspace = Workspace::factory()->for($user)->create([
            'title' => $workspaceTitle
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/workspaces/{$workspace->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', $workspaceTitle);
    }

    public function test_create_workspace()
    {
        $user = User::factory()->create();
        $data = [
            'title' => 'Testing Workspace',
            'user_id' => $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/workspaces', $data);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $data['title'])
                    ->where('data.user_id', $data['user_id'])
                    ->etc()
            );
    }

    public function test_update_workspace()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();
        $data = [
            'title' => 'Testing Workspace Updated'
        ];

        $response = $this->actingAs($user)->patchJson("/api/v1/workspaces/{$workspace->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $data['title'])
                    ->etc()
            );
    }


    public function test_delete_workspace()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/api/v1/workspaces/{$workspace->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($workspace);
    }
}

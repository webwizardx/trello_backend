<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_login()
    {
        $email = 'john@test.com';
        $password = 'password';
        $user = User::factory()->create([
            'email' => $email,
            'password' => $password
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            "email" => $email,
            "password" => $password
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.user.email', $user['email'])
                    ->missing('data.user.password')
                    ->etc()
            );
    }

    public function test_post_signup()
    {
        $user = [
            "name" => "John",
            "last_name" => "Doe",
            "email" => "john@doe.com",
            "username" => "26838816",
            "password" => "password"
        ];

        $response = $this->postJson('/api/v1/auth/signup', $user);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.name', $user['name'])
                    ->where('data.last_name', $user['last_name'])
                    ->where('data.email', $user['email'])
                    ->where('data.username', $user['username'])
                    ->missing('data.password')
                    ->etc()
            );
    }

    public function test_get_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api');
        $headers = [
            'Authorization' => "Bearer {$token->plainTextToken}"
        ];

        $response = $this->withHeaders($headers)->getJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    }
}

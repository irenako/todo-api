<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_create_user()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123.'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User created successfully']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'login' => 'johndoe'
        ]);
    }

    public function test_cannot_create_user_with_invalid_password()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123!'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_existing_email()
    {
        User::factory()->create([
            'email' => 'john@example.com'
        ]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123!'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_existing_login()
    {
        User::factory()->create([
            'login' => 'johndoe'
        ]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123!'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_lowercase_first_name()
    {
        $userData = [
            'first_name' => 'john',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123!'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_lowercase_last_name()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'doe',
            'email' => 'john@example.com',
            'login' => 'johndoe',
            'password' => 'Password123!'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_can_show_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);

        $this->assertEquals($user->id, $response->json('data')['id']);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe'
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'User updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe'
        ]);
    }

    public function test_cannot_update_user_with_lowercase_first_name()
    {
        $user = User::factory()->create();

        $updateData = [
            'first_name' => 'john', 
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_cannot_update_user_with_lowercase_last_name()
    {
        $user = User::factory()->create();

        $updateData = [
            'last_name' => 'doe', 
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_cannot_update_user_with_repeated_login()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $updateData = [
            'login' => $user2->login, 
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_cannot_update_user_with_repeated_email()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $updateData = [
            'email' => $user2->email, 
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    public function test_can_sort_users_by_first_name_asc()
    {
        User::factory()->create(['first_name' => 'Alice']);
        User::factory()->create(['first_name' => 'Bob']);
        User::factory()->create(['first_name' => 'Charlie']);

        $response = $this->getJson('/api/users?sort_by=first_name&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Alice', $data[0]['first_name']);
        $this->assertEquals('Bob', $data[1]['first_name']);
        $this->assertEquals('Charlie', $data[2]['first_name']);
    }

    public function test_can_sort_users_by_first_name_desc()
    {
        User::factory()->create(['first_name' => 'Alice']);
        User::factory()->create(['first_name' => 'Bob']);
        User::factory()->create(['first_name' => 'Charlie']);

        $response = $this->getJson('/api/users?sort_by=first_name&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Charlie', $data[0]['first_name']);
        $this->assertEquals('Bob', $data[1]['first_name']);
        $this->assertEquals('Alice', $data[2]['first_name']);
    }

    public function test_can_sort_users_by_last_name_asc()
    {
        User::factory()->create(['last_name' => 'Smith']);
        User::factory()->create(['last_name' => 'Johnson']);
        User::factory()->create(['last_name' => 'Williams']);

        $response = $this->getJson('/api/users?sort_by=last_name&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Johnson', $data[0]['last_name']);
        $this->assertEquals('Smith', $data[1]['last_name']);
        $this->assertEquals('Williams', $data[2]['last_name']);
    }

    public function test_can_sort_users_by_last_name_desc()
    {
        User::factory()->create(['last_name' => 'Smith']);
        User::factory()->create(['last_name' => 'Johnson']);
        User::factory()->create(['last_name' => 'Williams']);

        $response = $this->getJson('/api/users?sort_by=last_name&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Williams', $data[0]['last_name']);
        $this->assertEquals('Smith', $data[1]['last_name']);
        $this->assertEquals('Johnson', $data[2]['last_name']);
    }

    public function test_can_sort_users_by_email_asc()
    {
        User::factory()->create(['email' => 'abby@example.com']);
        User::factory()->create(['email' => 'bob@example.com']);
        User::factory()->create(['email' => 'charlie@example.com']);

        $response = $this->getJson('/api/users?sort_by=email&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('abby@example.com', $data[0]['email']);
        $this->assertEquals('bob@example.com', $data[1]['email']);
        $this->assertEquals('charlie@example.com', $data[2]['email']);
    }

    public function test_can_sort_users_by_email_desc()
    {
        User::factory()->create(['email' => 'abby@example.com']);
        User::factory()->create(['email' => 'bob@example.com']);
        User::factory()->create(['email' => 'charlie@example.com']);

        $response = $this->getJson('/api/users?sort_by=email&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('charlie@example.com', $data[0]['email']);
        $this->assertEquals('bob@example.com', $data[1]['email']);
        $this->assertEquals('abby@example.com', $data[2]['email']);
    }

    public function test_cannot_sort_users_by_invalid_field()
    {
        User::factory()->create(['email' => 'abby@example.com']);
        User::factory()->create(['email' => 'bob@example.com']);
        User::factory()->create(['email' => 'charlie@example.com']);

        $response = $this->getJson('/api/users?sort_by=login&sort_order=desc');

        $response->assertStatus(422);
    }

    public function test_cannot_sort_users_by_invalid_order()
    {
        User::factory()->create(['email' => 'abby@example.com']);
        User::factory()->create(['email' => 'bob@example.com']);
        User::factory()->create(['email' => 'charlie@example.com']);

        $response = $this->getJson('/api/users?sort_by=email&sort_order=abc');

        $response->assertStatus(422);
    }

    public function test_can_paginate_users()
    {
        User::factory()->count(15)->create();

        $response = $this->getJson('/api/users?per_page=10');

        $response->assertStatus(200);

        $this->assertEquals(15, $response->json('meta')['total']);
        $this->assertEquals(10, $response->json('meta')['per_page']);
        $this->assertEquals(1, $response->json('meta')['current_page']);
        $this->assertEquals(2, $response->json('meta')['last_page']);
    }
}

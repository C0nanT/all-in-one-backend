<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

test('register returns token and user with valid data', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email'],
        ])
        ->assertJson([
            'user' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ],
        ]);

    expect($response->json('token'))->not->toBeEmpty();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('register fails with duplicate email', function (): void {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('register fails when password confirmation does not match', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('login returns token with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'secret123',
        'device_name' => 'Test Device',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['token'])
        ->assertJsonMissing(['user']);

    expect($response->json('token'))->not->toBeEmpty();
});

test('login fails with invalid credentials', function (): void {
    User::factory()->create(['email' => 'user@example.com']);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
        'device_name' => 'Test Device',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('user route returns 401 without token', function (): void {
    $response = $this->getJson('/api/user');

    $response->assertUnauthorized()
        ->assertJsonPath('data', [])
        ->assertJsonPath('meta.error', 'Unauthenticated.');
});

test('user route returns authenticated user with valid token', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/user');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('logout revokes token and returns 204', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/logout');

    $response->assertNoContent();
});

test('after logout token is invalid for user route', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $user->tokens()->delete();
    $this->assertDatabaseCount('personal_access_tokens', 0);

    $response = $this->getJson('/api/user', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertUnauthorized()
        ->assertJsonPath('data', [])
        ->assertJsonPath('meta.error', 'Unauthenticated.');
});

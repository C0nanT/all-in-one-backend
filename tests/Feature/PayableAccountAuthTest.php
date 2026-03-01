<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('payable account routes return 401 without token', function (): void {
    $response = $this->getJson('/api/payable-accounts');

    $response->assertUnauthorized()
        ->assertJsonPath('data', [])
        ->assertJsonPath('meta.error', 'Unauthenticated.');
});

test('payable account counts route returns 401 without token', function (): void {
    $response = $this->getJson('/api/payable-accounts/counts?period=2026-01');

    $response->assertUnauthorized();
});

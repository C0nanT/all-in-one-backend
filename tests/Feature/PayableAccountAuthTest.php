<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('payable account routes return 401 without token', function (): void {
    $response = $this->getJson('/api/payable-accounts');

    $response->assertUnauthorized();
});

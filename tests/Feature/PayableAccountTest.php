<?php

use App\Models\PayableAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

test('index returns all payable accounts', function (): void {
    PayableAccount::factory()->count(2)->create();

    $response = $this->getJson('/api/payable-accounts');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'payments', 'created_at', 'updated_at'],
            ],
        ]);
});

test('index returns empty when there are no accounts', function (): void {
    $response = $this->getJson('/api/payable-accounts');

    $response->assertSuccessful()
        ->assertJsonPath('data', []);
});

test('can create payable account with valid data', function (): void {
    $response = $this->postJson('/api/payable-accounts', [
        'name' => 'Supplier XYZ',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Supplier XYZ');

    $this->assertDatabaseHas('payable_accounts', [
        'name' => 'Supplier XYZ',
    ]);
});

test('creation fails without name', function (): void {
    $response = $this->postJson('/api/payable-accounts', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('shows a payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create(['name' => 'Unique account']);

    $response = $this->getJson("/api/payable-accounts/{$payableAccount->id}");

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $payableAccount->id)
        ->assertJsonPath('data.name', 'Unique account');
});

test('show returns 404 for non-existent account', function (): void {
    $response = $this->getJson('/api/payable-accounts/99999');

    $response->assertNotFound();
});

test('can update payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create([
        'name' => 'Before',
    ]);

    $response = $this->putJson("/api/payable-accounts/{$payableAccount->id}", [
        'name' => 'After',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'After');

    $payableAccount->refresh();
    expect($payableAccount->name)->toBe('After');
});

test('can update partially', function (): void {
    $payableAccount = PayableAccount::factory()->create([
        'name' => 'Original',
    ]);

    $response = $this->patchJson("/api/payable-accounts/{$payableAccount->id}", [
        'name' => 'Updated',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated');
});

test('can delete payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();

    $response = $this->deleteJson("/api/payable-accounts/{$payableAccount->id}");

    $response->assertNoContent();
    $this->assertSoftDeleted('payable_accounts', ['id' => $payableAccount->id]);
});

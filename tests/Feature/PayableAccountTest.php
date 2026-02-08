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
                '*' => ['id', 'name', 'amount', 'status', 'created_at', 'updated_at'],
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
        'amount' => 1500.50,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Supplier XYZ')
        ->assertJsonPath('data.amount', 1500.5)
        ->assertJsonPath('data.status', 'open');

    $this->assertDatabaseHas('payable_accounts', [
        'name' => 'Supplier XYZ',
        'amount' => 1500.50,
        'status' => 'open',
    ]);
});

test('can create payable account with explicit status', function (): void {
    $response = $this->postJson('/api/payable-accounts', [
        'name' => 'Paid account',
        'amount' => 100,
        'status' => 'paid',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'paid');
});

test('creation fails without name', function (): void {
    $response = $this->postJson('/api/payable-accounts', [
        'amount' => 100,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('creation fails without amount', function (): void {
    $response = $this->postJson('/api/payable-accounts', [
        'name' => 'Some account',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

test('creation fails with invalid status', function (): void {
    $response = $this->postJson('/api/payable-accounts', [
        'name' => 'Some account',
        'amount' => 100,
        'status' => 'invalid',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
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
        'amount' => 100,
        'status' => \App\PayableAccountStatus::Open,
    ]);

    $response = $this->putJson("/api/payable-accounts/{$payableAccount->id}", [
        'name' => 'After',
        'amount' => 200,
        'status' => 'paid',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'After')
        ->assertJsonPath('data.status', 'paid');
    expect((float) $response->json('data.amount'))->toBe(200.0);

    $payableAccount->refresh();
    expect($payableAccount->name)->toBe('After')
        ->and((float) $payableAccount->amount)->toBe(200.0)
        ->and($payableAccount->status->value)->toBe('paid');
});

test('can update partially', function (): void {
    $payableAccount = PayableAccount::factory()->create([
        'name' => 'Original',
        'amount' => 50,
    ]);

    $response = $this->patchJson("/api/payable-accounts/{$payableAccount->id}", [
        'status' => 'paid',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'Original')
        ->assertJsonPath('data.status', 'paid');
    expect((float) $response->json('data.amount'))->toBe(50.0);
});

test('can delete payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();

    $response = $this->deleteJson("/api/payable-accounts/{$payableAccount->id}");

    $response->assertNoContent();
    $this->assertSoftDeleted('payable_accounts', ['id' => $payableAccount->id]);
});

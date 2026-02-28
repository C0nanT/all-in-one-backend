<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Models\PayableAccountPayment;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

test('index returns all payable accounts', function (): void {
    PayableAccount::factory()->count(2)->create();

    $response = $this->getJson('/api/payable-accounts?period=2026-01');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'status', 'payment', 'created_at', 'updated_at'],
            ],
        ]);
});

test('index returns empty when there are no accounts', function (): void {
    $response = $this->getJson('/api/payable-accounts?period=2026-01');

    $response->assertSuccessful()
        ->assertJsonPath('data', []);
});

test('index filters payments by period when period query param is passed', function (): void {
    $account = PayableAccount::factory()->create(['name' => 'Account with payments']);
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 100,
        'payer_id' => $this->user->id,
        'period' => '2026-01-01',
    ]);
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 200,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);

    $response = $this->getJson('/api/payable-accounts?period=2026-02');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Account with payments')
        ->assertJsonPath('data.0.payment.period', '01-02-2026');
    expect((float) $response->json('data.0.payment.amount'))->toBe(200.0);
});

test('index returns status paid_zero when payment amount is zero', function (): void {
    $account = PayableAccount::factory()->create(['name' => 'Zero amount account']);
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 0,
        'payer_id' => $this->user->id,
        'period' => '2026-01-01',
    ]);

    $response = $this->getJson('/api/payable-accounts?period=2026-01');

    $response->assertSuccessful()
        ->assertJsonPath('data.0.status', 'paid_zero');
    expect((float) $response->json('data.0.payment.amount'))->toBe(0.0);
});

test('index returns payment for period when period query param is passed', function (): void {
    $account = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 100,
        'payer_id' => $this->user->id,
        'period' => '2026-01-01',
    ]);
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 200,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);

    $response = $this->getJson('/api/payable-accounts?period=2026-02');

    $response->assertSuccessful();
    expect((float) $response->json('data.0.payment.amount'))->toBe(200.0);
});

test('index returns 422 when period query param is invalid', function (): void {
    $response = $this->getJson('/api/payable-accounts?period=invalid');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
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

    $response->assertNotFound()
        ->assertJsonPath('data', [])
        ->assertJsonPath('meta.error', 'Resource not found.');
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

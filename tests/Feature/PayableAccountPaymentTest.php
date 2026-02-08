<?php

use App\Models\PayableAccount;
use App\Models\PayableAccountPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

test('can store payment for payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 1500.50,
        'payer_id' => $payer->id,
        'period' => '2026-02-15',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.payable_account_id', $payableAccount->id)
        ->assertJsonPath('data.amount', 1500.5)
        ->assertJsonPath('data.payer_id', $payer->id)
        ->assertJsonPath('data.period', '2026-02-01');

    $payment = PayableAccountPayment::query()->where('payable_account_id', $payableAccount->id)->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount)->toBe(1500.5)
        ->and($payment->period->format('Y-m-d'))->toBe('2026-02-01');
});

test('store payment fails without amount', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'payer_id' => $payer->id,
        'period' => '2026-02-01',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

test('store payment fails without payer_id', function (): void {
    $payableAccount = PayableAccount::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 100,
        'period' => '2026-02-01',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['payer_id']);
});

test('store payment fails with non-existent payer_id', function (): void {
    $payableAccount = PayableAccount::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 100,
        'payer_id' => 99999,
        'period' => '2026-02-01',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['payer_id']);
});

test('store payment fails without period', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 100,
        'payer_id' => $payer->id,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});

test('store payment returns 404 for non-existent payable account', function (): void {
    $payer = User::factory()->create();

    $response = $this->postJson('/api/payable-accounts/99999/payments', [
        'amount' => 100,
        'payer_id' => $payer->id,
        'period' => '2026-02-01',
    ]);

    $response->assertNotFound();
});

test('store payment fails when same period already exists for payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();
    $period = '2026-02-01';

    $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 100,
        'payer_id' => $payer->id,
        'period' => $period,
    ])->assertCreated();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 200,
        'payer_id' => $payer->id,
        'period' => $period,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});

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

test('can store payment for payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $response = $this->postJson("/api/payable-accounts/{$payableAccount->id}/payments", [
        'amount' => 1500.50,
        'payer_id' => $payer->id,
        'period' => '2026-02-15',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.amount', 1500.5)
        ->assertJsonPath('data.period', '01-02-2026');

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

test('can update payment for payable account', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();
    $otherPayer = User::factory()->create();

    $payment = PayableAccountPayment::factory()->create([
        'payable_account_id' => $payableAccount->id,
        'payer_id' => $payer->id,
        'amount' => 500,
        'period' => '2026-02-01',
    ]);

    $response = $this->putJson(
        "/api/payable-accounts/{$payableAccount->id}/payments/{$payment->id}",
        [
            'amount' => 1200.75,
            'payer_id' => $otherPayer->id,
            'period' => '2026-03-15',
        ]
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.amount', 1200.75)
        ->assertJsonPath('data.payer_id', $otherPayer->id)
        ->assertJsonPath('data.period', '01-03-2026');

    $payment->refresh();
    expect((float) $payment->amount)->toBe(1200.75)
        ->and($payment->payer_id)->toBe($otherPayer->id)
        ->and($payment->period->format('Y-m-d'))->toBe('2026-03-01');
});

test('update payment can keep same period when editing other fields', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();
    $otherPayer = User::factory()->create();

    $payment = PayableAccountPayment::factory()->create([
        'payable_account_id' => $payableAccount->id,
        'payer_id' => $payer->id,
        'amount' => 500,
        'period' => '2026-02-01',
    ]);

    $response = $this->putJson(
        "/api/payable-accounts/{$payableAccount->id}/payments/{$payment->id}",
        [
            'amount' => 750,
            'payer_id' => $otherPayer->id,
            'period' => '2026-02-01',
        ]
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.period', '01-02-2026');
});

test('update payment fails when period conflicts with another payment', function (): void {
    $payableAccount = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $payment1 = PayableAccountPayment::factory()->create([
        'payable_account_id' => $payableAccount->id,
        'payer_id' => $payer->id,
        'amount' => 100,
        'period' => '2026-02-01',
    ]);

    $payment2 = PayableAccountPayment::factory()->create([
        'payable_account_id' => $payableAccount->id,
        'payer_id' => $payer->id,
        'amount' => 200,
        'period' => '2026-03-01',
    ]);

    $response = $this->putJson(
        "/api/payable-accounts/{$payableAccount->id}/payments/{$payment2->id}",
        [
            'amount' => 250,
            'payer_id' => $payer->id,
            'period' => '2026-02-01',
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});

test('update payment returns 404 when payment belongs to different account', function (): void {
    $account1 = PayableAccount::factory()->create();
    $account2 = PayableAccount::factory()->create();
    $payer = User::factory()->create();

    $payment = PayableAccountPayment::factory()->create([
        'payable_account_id' => $account1->id,
        'payer_id' => $payer->id,
        'amount' => 100,
        'period' => '2026-02-01',
    ]);

    $response = $this->putJson(
        "/api/payable-accounts/{$account2->id}/payments/{$payment->id}",
        [
            'amount' => 200,
            'payer_id' => $payer->id,
            'period' => '2026-02-01',
        ]
    );

    $response->assertNotFound();
});

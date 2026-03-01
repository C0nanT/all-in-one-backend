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

test('counts returns paid and unpaid for period with mixed accounts', function (): void {
    $paidAccount = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $paidAccount->id,
        'amount' => 100,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);
    PayableAccount::factory()->create(); // unpaid

    $response = $this->getJson('/api/payable-accounts/counts?period=2026-02');

    $response->assertSuccessful()
        ->assertJsonPath('data.paid', 1)
        ->assertJsonPath('data.unpaid', 1);
});

test('counts returns all unpaid when no payments in period', function (): void {
    PayableAccount::factory()->count(3)->create();

    $response = $this->getJson('/api/payable-accounts/counts?period=2026-01');

    $response->assertSuccessful()
        ->assertJsonPath('data.paid', 0)
        ->assertJsonPath('data.unpaid', 3);
});

test('counts returns all paid when all accounts have payments in period', function (): void {
    $account1 = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account1->id,
        'amount' => 50,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);
    $account2 = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account2->id,
        'amount' => 0,
        'payer_id' => null,
        'period' => '2026-02-01',
    ]);

    $response = $this->getJson('/api/payable-accounts/counts?period=2026-02');

    $response->assertSuccessful()
        ->assertJsonPath('data.paid', 2)
        ->assertJsonPath('data.unpaid', 0);
});

test('counts uses latest payment when account has multiple in period', function (): void {
    $account = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 100,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);
    PayableAccountPayment::query()->create([
        'payable_account_id' => $account->id,
        'amount' => 200,
        'payer_id' => $this->user->id,
        'period' => '2026-02-15',
    ]);

    $response = $this->getJson('/api/payable-accounts/counts?period=2026-02');

    $response->assertSuccessful()
        ->assertJsonPath('data.paid', 1)
        ->assertJsonPath('data.unpaid', 0);
});

test('counts filters by period and ignores other months', function (): void {
    $accountInPeriod = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $accountInPeriod->id,
        'amount' => 100,
        'payer_id' => $this->user->id,
        'period' => '2026-02-01',
    ]);
    $accountOtherMonth = PayableAccount::factory()->create();
    PayableAccountPayment::query()->create([
        'payable_account_id' => $accountOtherMonth->id,
        'amount' => 50,
        'payer_id' => $this->user->id,
        'period' => '2026-01-01',
    ]);

    $response = $this->getJson('/api/payable-accounts/counts?period=2026-02');

    $response->assertSuccessful()
        ->assertJsonPath('data.paid', 1)
        ->assertJsonPath('data.unpaid', 1);
});

test('counts returns 422 when period is missing', function (): void {
    $response = $this->getJson('/api/payable-accounts/counts');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});

test('counts returns 422 when period is invalid', function (): void {
    $response = $this->getJson('/api/payable-accounts/counts?period=invalid');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});

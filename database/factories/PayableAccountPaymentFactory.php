<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Models\PayableAccountPayment;
use Modules\User\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\PayableAccount\Models\PayableAccountPayment>
 */
class PayableAccountPaymentFactory extends Factory
{
    protected $model = PayableAccountPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payable_account_id' => PayableAccount::factory(),
            'payer_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'period' => Carbon::now()->startOfMonth(),
        ];
    }
}

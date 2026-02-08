<?php

namespace Database\Factories;

use App\Models\PayableAccount;
use App\PayableAccountStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayableAccount>
 */
class PayableAccountFactory extends Factory
{
    protected $model = PayableAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account' => fake()->words(3, true),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'status' => PayableAccountStatus::Open,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PayableAccountStatus::Paid]);
    }
}

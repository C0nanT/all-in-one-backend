<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PayableAccount\Models\PayableAccount;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\PayableAccount\Models\PayableAccount>
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
            'name' => fake()->words(3, true),
        ];
    }
}

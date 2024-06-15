<?php

namespace Homeful\Availments\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Homeful\Availments\Models\Availment;

class AvailmentFactory extends Factory
{
    protected $model = Availment::class;

    public function definition()
    {
        return [
            'reference_code' => $this->faker->word(),
            'sku' => $this->faker->word(),
            'processing_fee' => $this->faker->numberBetween(10000, 20000),
            'loan_amount' => $this->faker->numberBetween(850000, 3000000),
            'down_payment_monthly_amortization' => $this->faker->numberBetween(100000, 200000),
            'down_payment_months_to_pay' => $this->faker->numberBetween(12, 24),
            'balance_payment_monthly_amortization' => $this->faker->numberBetween(700000, 800000),
            'balance_payment_months_to_pay' => $this->faker->numberBetween(300, 320),
            'balance_payment_annual_interest' => $this->faker->numberBetween(3, 7)/100,
            'seller_commission_code' => $this->faker->word(),
            'loan_computation' => [],
        ];
    }
}

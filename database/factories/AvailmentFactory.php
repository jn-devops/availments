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
            'product_sku' => $this->faker->word(),
            'holding_fee' => $this->faker->numberBetween(10000, 30000),
            'total_contract_price' => $this->faker->numberBetween(850000, 3000000),
            'percent_miscellaneous_fees' => $this->faker->numberBetween(80, 90)/1000,
            'percent_down_payment' => $this->faker->numberBetween(5, 10)/100,
            'total_contract_price_balance_down_payment_term' => $this->faker->numberBetween(0, 12),
            'loan_term' => $this->faker->numberBetween(10, 30),
            'loan_interest' => $this->faker->numberBetween(3, 7)/100,
            'low_cash_out_amount' => 30000
        ];
    }
}

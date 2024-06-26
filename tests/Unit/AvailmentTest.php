<?php

use Brick\Money\Money;
use Homeful\Availments\Data\AvailmentData;
use Homeful\Availments\Models\Availment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Whitecube\Price\Price;

uses(RefreshDatabase::class, WithFaker::class);

dataset('agapeya-70-50-duplex', function () {
    return [
        [
            [
                'product_sku' => 'JN-AGM-CL-HLDUS-GRN',
                'holding_fee' => new Price(Money::of(10000, 'PHP')),
                'total_contract_price' => new Price(Money::of(2500000, 'PHP')),
                'percent_miscellaneous_fees' => 8.5 / 100,
                'percent_down_payment' => 5 / 100,
                'total_contract_price_balance_down_payment_term' => 12,
                'loan_term' => 20,
                'loan_interest' => 7 / 100,
            ],
        ],
    ];
});

dataset('ter-je-2br-40', function () {
    return [
        [
            [
                'product_sku' => 'JN-TERJE-BL-CS-2BREU-R',
                'holding_fee' => new Price(Money::of(10000, 'PHP')),
                'total_contract_price' => new Price(Money::of(4500000, 'PHP')),
                'percent_miscellaneous_fees' => 8.5 / 100,
                'percent_down_payment' => 5 / 100,
                'total_contract_price_balance_down_payment_term' => 12,
                'loan_term' => 20,
                'loan_interest' => 7 / 100,
            ],
        ],
    ];
});

dataset('promo_1', function () {
    return [
        [
            [
                'product_sku' => 'JN-AGM-CL-HLDUS-GRN',
                'holding_fee' => new Price(Money::of(10000, 'PHP')), //low-cash promo
                'balance_cash_out' => new Price(Money::of(20000, 'PHP')), //low-cash promo
                'total_contract_price' => new Price(Money::of(2500000, 'PHP')),
                'percent_miscellaneous_fees' => 5 / 100, //
                'percent_down_payment' => 0 / 100, //subsidized
                'total_contract_price_balance_down_payment_term' => 12,
                'loan_term' => 20,
                'loan_interest' => 7 / 100,
            ],
        ],
    ];
});

it('has attributes', function () {
    $availment = Availment::factory()->create();
    expect($availment->product_sku)->toBeString();
    expect($availment->holding_fee)->toBeInstanceOf(Price::class);
    expect($availment->total_contract_price)->toBeInstanceOf(Price::class);
    expect($availment->percent_miscellaneous_fees)->toBeFloat();
    expect($availment->miscellaneous_fees)->toBeInstanceOf(Price::class);
    expect($availment->net_total_contract_price)->toBeInstanceOf(Price::class);
    expect($availment->percent_down_payment)->toBeFloat();
    expect($availment->total_contract_price_down_payment_amount)->toBeInstanceOf(Price::class);
    expect($availment->total_contract_price_balance_down_payment_amount)->toBeInstanceOf(Price::class);
    expect($availment->total_contract_price_balance_down_payment_term)->toBeInt();
    expect($availment->total_contract_price_balance_down_payment_amortization_amount)->toBeInstanceOf(Price::class);
    expect($availment->miscellaneous_fees_down_payment_amount)->toBeInstanceOf(Price::class);
    expect($availment->percent_balance_payment)->toBeFloat();
    expect($availment->total_contract_price_balance_payment_amount)->toBeInstanceOf(Price::class);
    expect($availment->miscellaneous_fees_balance_payment_amount)->toBeInstanceOf(Price::class);
    expect($availment->loan_amount)->toBeInstanceOf(Price::class);
    expect($availment->loan_term)->toBeInt();
    expect($availment->loan_interest)->toBeFloat();
    expect($availment->loan_amortization_amount)->toBeInstanceOf(Price::class);
    expect($availment->low_cash_out_amount)->toBeInstanceOf(Price::class);
    expect($availment->balance_cash_out_amount)->toBeInstanceOf(Price::class);
    expect($availment->loan_object)->toBeNull();
    expect($availment->loan_data)->toBeNull();
});

it('can be persisted using associative array', function (array $attributes) {
    app(Availment::class)->create($attributes);
    $availment = Availment::where('product_sku', 'JN-AGM-CL-HLDUS-GRN')->first();
    if ($availment instanceof Availment) {
        expect($availment->product_sku)->toBe('JN-AGM-CL-HLDUS-GRN');
        expect($availment->holding_fee->inclusive()->compareTo(10000.0))->toBe(0); //C
        expect($availment->total_contract_price->inclusive()->compareTo(2500000.0))->toBe(0); //A
        expect($availment->percent_miscellaneous_fees)->toBe(8.5 / 100);
        expect($availment->miscellaneous_fees->inclusive()->compareTo(2500000.0 * 8.5 / 100))->toBe(0); //a
        expect($availment->miscellaneous_fees->inclusive()->compareTo(212500.0))->toBe(0); //a
        expect($availment->net_total_contract_price->inclusive()->compareTo(2500000.0 * (1 + 8.5 / 100)))->toBe(0); //b
        expect($availment->net_total_contract_price->inclusive()->compareTo(2712500.0))->toBe(0); //b
        expect($availment->percent_down_payment)->toBe(5 / 100);
        expect($availment->total_contract_price_down_payment_amount->inclusive()->compareTo(2500000.0 * 5 / 100))->toBe(0); //B
        expect($availment->total_contract_price_down_payment_amount->inclusive()->compareTo(125000.0))->toBe(0); //B
        expect($availment->total_contract_price_balance_down_payment_amount->inclusive()->compareTo(125000.0 - 10000.0))->toBe(0); //D
        expect($availment->total_contract_price_balance_down_payment_amount->inclusive()->compareTo(115000.0))->toBe(0); //D
        expect($availment->total_contract_price_balance_down_payment_term)->toBe(12);
        expect($availment->total_contract_price_balance_down_payment_amortization_amount->inclusive()->compareTo($availment->total_contract_price_balance_down_payment_amount->inclusive()->dividedBy($availment->total_contract_price_balance_down_payment_term, roundingMode: \Brick\Math\RoundingMode::CEILING)))->toBe(0); //E
        expect($availment->total_contract_price_balance_down_payment_amortization_amount->inclusive()->compareTo(9583.34))->toBe(0); //E
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo($availment->miscellaneous_fees->inclusive()->multipliedBy($availment->percent_down_payment, roundingMode: \Brick\Math\RoundingMode::CEILING)))->toBe(0); //c
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo(212500.0 * 5 / 100))->toBe(0); //c
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo(10625.0))->toBe(0); //c
        expect($availment->percent_balance_payment)->toBe(95 / 100);
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(2500000.0 * 95 / 100))->toBe(0); //F
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(2375000.0))->toBe(0); //F
        expect($availment->miscellaneous_fees_balance_payment_amount->inclusive()->compareTo(212500.0 * 95 / 100))->toBe(0); //d
        expect($availment->miscellaneous_fees_balance_payment_amount->inclusive()->compareTo(201875.0))->toBe(0); //d
        expect($availment->loan_amount->inclusive()->compareTo(2375000.0 + 201875.0))->toBe(0); //G
        expect($availment->loan_amount->inclusive()->compareTo(2576875.0))->toBe(0); //G
        expect($availment->loan_term)->toBe(20);
        expect($availment->loan_interest)->toBe(7 / 100);
        expect($availment->loan_amortization_amount->inclusive()->compareTo(19978.0))->toBe(0); //H
        expect($availment->low_cash_out_amount->inclusive()->compareTo(0))->toBe(0);
        expect($availment->balance_cash_out_amount->inclusive()->compareTo(0.0))->toBe(0);
        $availment->loan_term = 25;
        $availment->save();
        expect($availment->loan_amortization_amount->inclusive()->compareTo(18213.0))->toBe(0); //H
        $availment->loan_term = 30;
        $availment->save();
        expect($availment->loan_amortization_amount->inclusive()->compareTo(17144.0))->toBe(0); //H

        $availment->percent_miscellaneous_fees = 5 / 100;
        $availment->low_cash_out_amount = 30000; //C
        $availment->save();
        expect($availment->miscellaneous_fees->inclusive()->compareTo(2500000.0 * 5 / 100))->toBe(0); //a
        expect($availment->miscellaneous_fees->inclusive()->compareTo(125000.0))->toBe(0); //a
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(2375000.0))->toBe(0); //F
        expect($availment->loan_amount->inclusive()->compareTo(2375000.0 + 125000.0))->toBe(0); //a+F
        expect($availment->balance_cash_out_amount->inclusive()->compareTo(30000 - 10000))->toBe(0);
        expect($availment->loan_amortization_amount->inclusive()->compareTo(16633.0))->toBe(0);
    }
})->with('agapeya-70-50-duplex');

it('has computed attributes and can be updated indirectly', function (array $attributes) {
    app(Availment::class)->create($attributes);
    $availment = Availment::where('product_sku', 'JN-AGM-CL-HLDUS-GRN')->first();
    if ($availment instanceof Availment) {
        $availment->total_contract_price = 3000000;
        $availment->save();
        expect($availment->miscellaneous_fees->inclusive()->compareTo(3000000 * 8.5 / 100))->toBe(0);
        $availment->percent_miscellaneous_fees = 9 / 100;
        $availment->save();
        expect($availment->miscellaneous_fees->inclusive()->compareTo(3000000 * 9 / 100))->toBe(0);
    }
})->with('agapeya-70-50-duplex');

it('can have sample condominium computation', function (array $attributes) {
    app(Availment::class)->create($attributes);
    $availment = Availment::where('product_sku', 'JN-TERJE-BL-CS-2BREU-R')->first();
    if ($availment instanceof Availment) {
        expect($availment->product_sku)->toBe('JN-TERJE-BL-CS-2BREU-R');
        expect($availment->holding_fee->inclusive()->compareTo(10000.0))->toBe(0); //C
        expect($availment->total_contract_price->inclusive()->compareTo(4500000.0))->toBe(0); //A
        expect($availment->percent_miscellaneous_fees)->toBe(8.5 / 100);
        expect($availment->miscellaneous_fees->inclusive()->compareTo(4500000.0 * 8.5 / 100))->toBe(0); //a
        expect($availment->miscellaneous_fees->inclusive()->compareTo(382500.0))->toBe(0); //a
        expect($availment->net_total_contract_price->inclusive()->compareTo(4500000.0 * (1 + 8.5 / 100)))->toBe(0); //b
        expect($availment->net_total_contract_price->inclusive()->compareTo(4882500.0))->toBe(0); //b
        expect($availment->percent_down_payment)->toBe(5 / 100);
        expect($availment->total_contract_price_down_payment_amount->inclusive()->compareTo(4500000.0 * 5 / 100))->toBe(0); //B
        expect($availment->total_contract_price_down_payment_amount->inclusive()->compareTo(225000.0))->toBe(0); //B
        expect($availment->total_contract_price_balance_down_payment_amount->inclusive()->compareTo(225000.0 - 10000.0))->toBe(0); //D
        expect($availment->total_contract_price_balance_down_payment_amount->inclusive()->compareTo(215000.0))->toBe(0); //D
        expect($availment->total_contract_price_balance_down_payment_term)->toBe(12);
        expect($availment->total_contract_price_balance_down_payment_amortization_amount->inclusive()->compareTo($availment->total_contract_price_balance_down_payment_amount->inclusive()->dividedBy($availment->total_contract_price_balance_down_payment_term, roundingMode: \Brick\Math\RoundingMode::CEILING)))->toBe(0); //E
        expect($availment->total_contract_price_balance_down_payment_amortization_amount->inclusive()->compareTo(17916.67))->toBe(0); //E
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo($availment->miscellaneous_fees->inclusive()->multipliedBy($availment->percent_down_payment, roundingMode: \Brick\Math\RoundingMode::CEILING)))->toBe(0); //c
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo(382500.0 * 5 / 100))->toBe(0); //c
        expect($availment->miscellaneous_fees_down_payment_amount->inclusive()->compareTo(19125.0))->toBe(0); //c
        expect($availment->percent_balance_payment)->toBe(95 / 100);
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(4500000.0 * 95 / 100))->toBe(0); //F
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(4275000.0))->toBe(0); //F
        expect($availment->miscellaneous_fees_balance_payment_amount->inclusive()->compareTo(382500.0 * 95 / 100))->toBe(0); //d
        expect($availment->miscellaneous_fees_balance_payment_amount->inclusive()->compareTo(363375.0))->toBe(0); //d
        expect($availment->loan_amount->inclusive()->compareTo(4275000.0 + 363375.0))->toBe(0); //G
        expect($availment->loan_amount->inclusive()->compareTo(4638375.0))->toBe(0); //G
        expect($availment->loan_term)->toBe(20);
        expect($availment->loan_interest)->toBe(7 / 100);
        expect($availment->loan_amortization_amount->inclusive()->compareTo(35961.0))->toBe(0); //H
        expect($availment->low_cash_out_amount->inclusive()->compareTo(0))->toBe(0);
        expect($availment->balance_cash_out_amount->inclusive()->compareTo(0.0))->toBe(0);
        $availment->loan_term = 25;
        $availment->save();
        expect($availment->loan_amortization_amount->inclusive()->compareTo(32783.0))->toBe(0); //H
        $availment->loan_term = 30;
        $availment->save();
        expect($availment->loan_amortization_amount->inclusive()->compareTo(30859.0))->toBe(0); //H

        $availment->percent_miscellaneous_fees = 5 / 100;
        $availment->low_cash_out_amount = 30000; //C
        $availment->save();
        expect($availment->miscellaneous_fees->inclusive()->compareTo(4500000.0 * 5 / 100))->toBe(0); //a
        expect($availment->miscellaneous_fees->inclusive()->compareTo(225000.0))->toBe(0); //a
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo(4275000.0))->toBe(0); //F
        expect($availment->loan_amount->inclusive()->compareTo(225000.0 + 4275000.0))->toBe(0); //a+F
        expect($availment->balance_cash_out_amount->inclusive()->compareTo(30000 - 10000))->toBe(0);
        expect($availment->loan_amortization_amount->inclusive()->compareTo(29939.0))->toBe(0);
    }
})->with('ter-je-2br-40');

it('has data', function (array $attributes) {
    app(Availment::class)->create($attributes);
    $model = Availment::where('product_sku', 'JN-AGM-CL-HLDUS-GRN')->first();
    if ($model instanceof Availment) {
        $data = AvailmentData::fromModel($model);
        expect($data->product_sku)->toBe($model->product_sku);
        expect($data->holding_fee)->toBe($model->holding_fee->inclusive()->getAmount()->toFloat());
        expect($data->total_contract_price)->toBe($model->total_contract_price->inclusive()->getAmount()->toFloat());
        expect($data->percent_miscellaneous_fees)->toBe($model->percent_miscellaneous_fees);
        expect($data->miscellaneous_fees)->toBe($model->miscellaneous_fees->inclusive()->getAmount()->toFloat());
        expect($data->net_total_contract_price)->toBe($model->net_total_contract_price->inclusive()->getAmount()->toFloat());
        expect($data->percent_down_payment)->toBe($model->percent_down_payment);
        expect($data->total_contract_price_down_payment_amount)->toBe($model->total_contract_price_down_payment_amount->inclusive()->getAmount()->toFloat());
        expect($data->total_contract_price_balance_down_payment_amount)->toBe($model->total_contract_price_balance_down_payment_amount->inclusive()->getAmount()->toFloat());
        expect($data->total_contract_price_balance_down_payment_term)->toBe($model->total_contract_price_balance_down_payment_term);
        expect($data->total_contract_price_balance_down_payment_amortization_amount)->toBe($model->total_contract_price_balance_down_payment_amortization_amount->inclusive()->getAmount()->toFloat());
        expect($data->miscellaneous_fees_down_payment_amount)->toBe($model->miscellaneous_fees_down_payment_amount->inclusive()->getAmount()->toFloat());
        expect($data->percent_balance_payment)->toBe($model->percent_balance_payment);
        expect($data->total_contract_price_balance_payment_amount)->toBe($model->total_contract_price_balance_payment_amount->inclusive()->getAmount()->toFloat());
        expect($data->miscellaneous_fees_balance_payment_amount)->toBe($model->miscellaneous_fees_balance_payment_amount->inclusive()->getAmount()->toFloat());
        expect($data->loan_amount)->toBe($model->loan_amount->inclusive()->getAmount()->toFloat());
        expect($data->loan_term)->toBe($model->loan_term);
        expect($data->loan_interest)->toBe($model->loan_interest);
        expect($data->loan_amortization_amount)->toBe($model->loan_amortization_amount->inclusive()->getAmount()->toFloat());
        expect($data->low_cash_out_amount)->toBe($model->low_cash_out_amount->inclusive()->getAmount()->toFloat());
        expect($data->balance_cash_out_amount)->toBe($model->balance_cash_out_amount->inclusive()->getAmount()->toFloat());
    }
})->with('agapeya-70-50-duplex');

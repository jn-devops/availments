<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Propaganistas\LaravelPhone\PhoneNumber;
use Homeful\Availments\Models\Availment;
use Whitecube\Price\Price;
use Brick\Money\Money;

uses(RefreshDatabase::class, WithFaker::class);

it('has attributes', function () {
    $availment = Availment::factory()->create();
    expect($availment->reference_code)->toBeString();
    expect($availment->borrower_mobile)->toBeInstanceOf(PhoneNumber::class);
    expect($availment->product_sku)->toBeString();
    expect($availment->processing_fee)->toBeInstanceOf(Price::class);
    expect($availment->loan_amount)->toBeInstanceOf(Price::class);
    expect($availment->down_payment_monthly_amortization)->toBeInstanceOf(Price::class);
    expect($availment->down_payment_months_to_pay)->toBeInt();
    expect($availment->balance_payment_monthly_amortization)->toBeInstanceOf(Price::class);
    expect($availment->balance_payment_months_to_pay)->toBeInt();
    expect($availment->balance_payment_annual_interest)->toBeFloat();
    expect($availment->seller_commission_code)->toBeString();
    expect($availment->loan_computation)->toBeNull();
});

it('can be persisted using associative array', function () {
    $availment = new Availment([
        'borrower_mobile' => new PhoneNumber('09171234567', 'PH'),
        'product_sku' => 'JN-AGM-CL-HLDUS-GRN',
        'processing_fee' => new Price(Money::of(1000, 'PHP')),
        'loan_amount' => new Price(Money::of(850000, 'PHP')),
        'down_payment_monthly_amortization' => new Price(Money::of(100000, 'PHP')),
        'down_payment_months_to_pay' => 12,
        'balance_payment_monthly_amortization' => new Price(Money::of(800000, 'PHP')),
        'balance_payment_months_to_pay' => 300,
        'balance_payment_annual_interest' => 6.25/100,
        'seller_commission_code' => 'ABC001',
    ]);
    $availment->reference_code = 'JN-123456';
    $availment->save();

    $availment = Availment::where('product_sku', 'JN-AGM-CL-HLDUS-GRN')->first();
    expect($availment->borrower_mobile->equals(new PhoneNumber('09171234567', 'PH')))->toBeTrue();
    expect($availment->product_sku)->toBe('JN-AGM-CL-HLDUS-GRN');
    expect($availment->processing_fee->inclusive()->compareTo(Money::of(1000, 'PHP')))->toBe(0);
    expect($availment->loan_amount->inclusive()->compareTo(Money::of(850000, 'PHP')))->toBe(0);
    expect($availment->down_payment_monthly_amortization->inclusive()->compareTo(Money::of(100000, 'PHP')))->toBe(0);
    expect($availment->down_payment_months_to_pay)->toBe(12);
    expect($availment->balance_payment_monthly_amortization->inclusive()->compareTo(Money::of(800000, 'PHP')))->toBe(0);
    expect($availment->balance_payment_months_to_pay)->toBe(300);
    expect($availment->balance_payment_annual_interest)->toBe(6.25/100);
    expect($availment->seller_commission_code)->toBe('ABC001');
    expect($availment->reference_code)->toBe('JN-123456');
});

it('has price attributes that accept both price and major integer', function () {
    $availment = new Availment;
    $availment->borrower_mobile = new PhoneNumber('09171234567', 'PH');
    $availment->product_sku = 'JN-AGM-CL-HLDUS-GRN';
    $availment->processing_fee = new Price(Money::of(1000, 'PHP'));
    $availment->loan_amount = new Price(Money::of(850000, 'PHP'));
    $availment->down_payment_monthly_amortization = new Price(Money::of(100000, 'PHP'));
    $availment->down_payment_months_to_pay = 12;
    $availment->balance_payment_monthly_amortization = new Price(Money::of(800000, 'PHP'));
    $availment->balance_payment_months_to_pay = 300;
    $availment->balance_payment_annual_interest = 6.25/100;
    $availment->seller_commission_code = 'ABC001';
    $availment->reference_code = 'JN-123456';
    $availment->save();

    $availment = Availment::withMeta('reference->code', 'JN-123456')->first();
    expect($availment->processing_fee->inclusive()->getAmount()->toInt())->toBe(1000);
    expect($availment->loan_amount->inclusive()->getAmount()->toInt())->toBe(850000);
    expect($availment->down_payment_monthly_amortization->inclusive()->getAmount()->toInt())->toBe(100000);
    expect($availment->balance_payment_monthly_amortization->inclusive()->getAmount()->toInt())->toBe(800000);
    $availment->processing_fee = 222;
    $availment->loan_amount = 800000;
    $availment->down_payment_monthly_amortization = 120000;
    $availment->balance_payment_monthly_amortization = 700000;
    $availment->save();
    $availment = Availment::where('product_sku', 'JN-AGM-CL-HLDUS-GRN')->first();
    expect($availment->processing_fee->inclusive()->getAmount()->toInt())->toBe(222);
    expect($availment->loan_amount->inclusive()->getAmount()->toInt())->toBe(800000);
    expect($availment->down_payment_monthly_amortization->inclusive()->getAmount()->toInt())->toBe(120000);
    expect($availment->balance_payment_monthly_amortization->inclusive()->getAmount()->toInt())->toBe(700000);
});

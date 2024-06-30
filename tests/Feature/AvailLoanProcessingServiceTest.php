<?php

use Brick\Money\Money;
use Homeful\Availments\Actions\AvailLoanProcessingServiceAction;
use Homeful\Availments\Data\AvailmentData;
use Homeful\Availments\Models\Availment;
use Homeful\Common\Interfaces\BorrowerInterface;
//use Homeful\Availments\Interfaces\BorrowerInterface;
//use Homeful\Availments\Interfaces\PropertyInterface;
use Homeful\Common\Interfaces\PropertyInterface;
use Homeful\Loan\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Propaganistas\LaravelPhone\PhoneNumber;
use Whitecube\Price\Price;

uses(RefreshDatabase::class, WithFaker::class);

dataset('borrower-25yo-15k_gmi', function () {
    return [
        fn () => tap(Mock(BorrowerInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getBirthdate')->andReturn(Carbon::parse('1999-03-17'));
            $mock->shouldReceive('getRegional')->andReturn(true);
            $mock->shouldReceive('getWages')->andReturn(Money::of(15000, 'PHP'));
            $mock->shouldReceive('getMobile')->andReturn(new PhoneNumber('09171234567', 'PH'));
            $mock->shouldReceive('getSellerCommissionCode')->andReturn('ABC-123456');
        }),
    ];
});

dataset('borrower-30yo-25k_gmi', function () {
    return [
        fn () => tap(Mock(BorrowerInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getBirthdate')->andReturn(Carbon::parse('1994-03-17'));
            $mock->shouldReceive('getRegional')->andReturn(true);
            $mock->shouldReceive('getWages')->andReturn(Money::of(25000, 'PHP'));
            $mock->shouldReceive('getMobile')->andReturn(new PhoneNumber('09171234567', 'PH'));
            $mock->shouldReceive('getSellerCommissionCode')->andReturn('ABC-123456');
        }),
    ];
});

dataset('property-850k', function () {
    return [
        fn () => tap(Mock(PropertyInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getSKU')->andReturn('JN-AGM-CL-HLDUS-GRN');
            $mock->shouldReceive('getTotalContractPrice')->andReturn(new Price(Money::of(850000, 'PHP')));
            $mock->shouldReceive('getAppraisedValue')->andReturn(new Price(Money::of(850000, 'PHP')));
            $mock->shouldReceive('getProcessingFee')->andReturn(new Price(Money::of(10000, 'PHP')));

        }),
    ];
});

dataset('property-1M', function () {
    return [
        fn () => tap(Mock(PropertyInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getSKU')->andReturn('JN-AGM-CL-HLDUS-GRN');
            $mock->shouldReceive('getTotalContractPrice')->andReturn(new Price(Money::of(1000000, 'PHP')));
            $mock->shouldReceive('getAppraisedValue')->andReturn(new Price(Money::of(1000000, 'PHP')));
            $mock->shouldReceive('getProcessingFee')->andReturn(new Price(Money::of(10000, 'PHP')));

        }),
    ];
});

dataset('property-3M', function () {
    return [
        fn () => tap(Mock(PropertyInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getSKU')->andReturn('JN-AGM-CL-HLDUS-GRN');
            $mock->shouldReceive('getTotalContractPrice')->andReturn(new Price(Money::of(3000000, 'PHP')));
            $mock->shouldReceive('getAppraisedValue')->andReturn(new Price(Money::of(2900000, 'PHP')));
            $mock->shouldReceive('getProcessingFee')->andReturn(new Price(Money::of(10000, 'PHP')));

        }),
    ];
});

dataset('property-2.5M', function () {
    return [
        fn () => tap(Mock(PropertyInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getSKU')->andReturn('JN-AGM-CL-HLDUS-GRN');
            $mock->shouldReceive('getTotalContractPrice')->andReturn(new Price(Money::of(2500000, 'PHP')));
            $mock->shouldReceive('getAppraisedValue')->andReturn(new Price(Money::of(2400000, 'PHP')));
            $mock->shouldReceive('getProcessingFee')->andReturn(new Price(Money::of(10000, 'PHP')));
        }),
    ];
});

dataset('agapeya-promo', function () {
    return [
        fn () => ['total_contract_price' => 2500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 30000, 'loan_term' => 20, 'guess_miscellaneous_fees_balance_payment_amount' => 118750.0, 'guess_balance_cash_out_amount' => 20000, 'guess_loan_amortization_amount' => 19382.0],
        fn () => ['total_contract_price' => 2500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 30000, 'loan_term' => 25, 'guess_miscellaneous_fees_balance_payment_amount' => 118750.0, 'guess_balance_cash_out_amount' => 20000, 'guess_loan_amortization_amount' => 17669.0],
        fn () => ['total_contract_price' => 2500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 30000, 'loan_term' => 30, 'guess_miscellaneous_fees_balance_payment_amount' => 118750.0, 'guess_balance_cash_out_amount' => 20000, 'guess_loan_amortization_amount' => 16633.0],
        fn () => ['total_contract_price' => 4500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 50000, 'loan_term' => 20, 'guess_miscellaneous_fees_balance_payment_amount' => 213750.0, 'guess_balance_cash_out_amount' => 40000, 'guess_loan_amortization_amount' => 34888.0],
        fn () => ['total_contract_price' => 4500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 50000, 'loan_term' => 25, 'guess_miscellaneous_fees_balance_payment_amount' => 213750.0, 'guess_balance_cash_out_amount' => 40000, 'guess_loan_amortization_amount' => 31805.0],
        fn () => ['total_contract_price' => 4500000, 'percent_down_payment' => 5 / 100, 'loan_interest' => 7 / 100, 'percent_miscellaneous_fees' => 5 / 100, 'low_cash_out_amount' => 50000, 'loan_term' => 30, 'guess_miscellaneous_fees_balance_payment_amount' => 213750.0, 'guess_balance_cash_out_amount' => 40000, 'guess_loan_amortization_amount' => 29939.0],
    ];
});

it('can persist availment - agapeya 70/50 duplex (standard)', function (MockInterface $borrowerObject, MockInterface $propertyObject) {
    $percent_down_payment = 5 / 100;
    $loan_interest = 7 / 100;

    $availment = app(AvailLoanProcessingServiceAction::class)->run($borrowerObject, $propertyObject,
        array_filter(compact('percent_down_payment', 'loan_interest'))
    );

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
    }
})->with('borrower-25yo-15k_gmi', 'property-2.5M');

it('can persist availment - middle income (promo)', function (MockInterface $borrowerObject, MockInterface $propertyObject, array $attribs) {
    $total_contract_price = $attribs['total_contract_price'];
    $percent_down_payment = $attribs['percent_down_payment'];
    $loan_interest = $attribs['loan_interest'];
    $percent_miscellaneous_fees = $attribs['percent_miscellaneous_fees'];
    $low_cash_out_amount = $attribs['low_cash_out_amount'];
    $loan_term = $attribs['loan_term'];

    $guess_loan_amortization_amount = $attribs['guess_loan_amortization_amount'];
    $guess_balance_cash_out_amount = $attribs['guess_balance_cash_out_amount'];
    $guess_miscellaneous_fees_balance_payment_amount = $attribs['guess_miscellaneous_fees_balance_payment_amount'];

    $availment = app(AvailLoanProcessingServiceAction::class)->run($borrowerObject, $propertyObject,
        array_filter(compact('total_contract_price', 'percent_down_payment', 'loan_interest', 'percent_miscellaneous_fees', 'low_cash_out_amount', 'loan_term'))
    );
    if ($availment instanceof Availment) {
        expect($availment->percent_down_payment)->toBe($percent_down_payment);
        expect($availment->loan_interest)->toBe($loan_interest);
        expect($availment->percent_miscellaneous_fees)->toBe($percent_miscellaneous_fees);
        expect($availment->low_cash_out_amount->inclusive()->compareTo($low_cash_out_amount))->toBe(0);
        expect($availment->loan_term)->toBe($loan_term);
        expect($availment->total_contract_price->inclusive()->compareTo($total_contract_price))->toBe(0); //A
        expect($availment->miscellaneous_fees->inclusive()->compareTo($miscellaneous_fees = $total_contract_price * 5 / 100))->toBe(0); //a
        expect($availment->total_contract_price_balance_payment_amount->inclusive()->compareTo($total_contract_price_balance_payment_amount = $total_contract_price * (1 - $availment->percent_down_payment)))->toBe(0); //F
        expect($availment->loan_amount->inclusive()->compareTo($total_contract_price_balance_payment_amount + $miscellaneous_fees))->toBe(0); //a+F

        $holding_fee = $availment->holding_fee->inclusive()->getAmount()->toFloat();
        expect($availment->balance_cash_out_amount->inclusive()->compareTo($low_cash_out_amount - $holding_fee))->toBe(0);
        expect($availment->miscellaneous_fees_balance_payment_amount->inclusive()->compareTo($guess_miscellaneous_fees_balance_payment_amount))->toBe(0);
        expect($availment->balance_cash_out_amount->inclusive()->compareTo($guess_balance_cash_out_amount))->toBe(0);
        expect($availment->loan_amortization_amount->inclusive()->compareTo($guess_loan_amortization_amount))->toBe(0);
    }
})->with('borrower-25yo-15k_gmi', 'property-2.5M', 'agapeya-promo');

it('has loan object, data and array', function (MockInterface $borrowerObject, MockInterface $propertyObject) {
    $percent_down_payment = 5 / 100;
    $loan_interest = 7 / 100;

    $availment = app(AvailLoanProcessingServiceAction::class)->run($borrowerObject, $propertyObject,
        array_filter(compact('percent_down_payment', 'loan_interest'))
    );
    if ($availment instanceof Availment) {
        expect($availment->loan_object)->toBeInstanceOf(Loan::class);
        with(AvailmentData::fromModel($availment), function (AvailmentData $data) use ($availment) {
            expect($availment->toData())->toBeInstanceOf(AvailmentData::class);
            expect($data->loan_data->toArray())->toBe($availment->loan_data->toArray());
            expect($data->loan_data->toArray())->toBe($availment->loan_array);
        });
    }
})->with('borrower-25yo-15k_gmi', 'property-2.5M');

it('can be configured using setters', function (MockInterface $borrowerObject, MockInterface $propertyObject) {
    $availment = app(AvailLoanProcessingServiceAction::class)
        ->setPercentDownPayment(10 / 100)
        ->setPercentMiscellaneousFees(9 / 100)
        ->setLoanTerm(24)
        ->setTotalContractPriceBalanceDownPaymentTerm(10)
        ->setLoanInterest(4 / 100)
        ->setLowCashOutAmount(25000)
        ->run($borrowerObject, $propertyObject,
            []
        );
    if ($availment instanceof Availment) {
        expect($availment->percent_down_payment)->toBe(10 / 100);
        expect($availment->percent_miscellaneous_fees)->toBe(9 / 100);
        expect($availment->loan_term)->toBe(24);
        expect($availment->total_contract_price_balance_down_payment_term)->toBe(10);
        expect($availment->loan_interest)->toBe(4 / 100);
        expect($availment->low_cash_out_amount->inclusive()->compareTo(25000))->toBe(0);
    }
})->with('borrower-25yo-15k_gmi', 'property-2.5M');

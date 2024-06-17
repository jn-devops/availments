<?php

use Homeful\Availments\Interfaces\{BorrowerInterface, PropertyInterface};
use Homeful\Availments\Actions\AvailLoanProcessingServiceAction;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Propaganistas\LaravelPhone\PhoneNumber;
use Homeful\Availments\Models\Availment;
use Homeful\Loan\Data\LoanData;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Whitecube\Price\Price;
use Brick\Money\Money;

uses(RefreshDatabase::class, WithFaker::class);

it('can persist availment', function () {
    $borrowerObject = tap(Mock(BorrowerInterface::class), function (MockInterface $mock) {
        $mock->shouldReceive('getBirthdate')->andReturn(Carbon::parse('1999-03-17'));
        $mock->shouldReceive('getRegional')->andReturn(true);
        $mock->shouldReceive('getWages')->andReturn(Money::of(15000, 'PHP'));
        $mock->shouldReceive('getMobile')->andReturn(new PhoneNumber('09171234567', 'PH'));
        $mock->shouldReceive('getSellerCommissionCode')->andReturn('ABC-123456');
    });

    $propertyObject = tap(Mock(PropertyInterface::class), function (MockInterface $mock) {
        $mock->shouldReceive('getSKU')->andReturn('JN-1234567');
        $mock->shouldReceive('getTotalContractPrice')->andReturn(new Price(Money::of(850000, 'PHP')));
        $mock->shouldReceive('getAppraisedValue')->andReturn(new Price(Money::of(850000, 'PHP')));
        $mock->shouldReceive('getProcessingFee')->andReturn(new Price(Money::of(10000, 'PHP')));

    });
    $loanAmount = new Price(Money::of(765000, 'PHP'));
    $availment = app(AvailLoanProcessingServiceAction::class)->run($borrowerObject, $propertyObject, $loanAmount);
    if ($availment instanceof Availment) {
        expect($availment->reference_code)->toBeNull();
        expect($availment->borrower_mobile->equals(new PhoneNumber('09171234567', 'PH')))->toBeTrue();
        expect($availment->product_sku)->toBe('JN-1234567');
        expect($availment->processing_fee->inclusive()->compareTo(10000))->toBe(0);
        expect($availment->loan_amount->inclusive()->compareTo(765000))->toBe(0);
        expect($availment->loan_computation)->toBeInstanceOf(LoanData::class);
        expect($availment->balance_payment_monthly_amortization->inclusive()->compareTo($availment->loan_computation->monthly_amortization))->toBe(0);
        expect($availment->balance_payment_months_to_pay)->toBe($availment->loan_computation->months_to_pay);
        expect($availment->balance_payment_annual_interest)->toBe($availment->loan_computation->annual_interest);
        expect($availment->seller_commission_code)->toBe('ABC-123456');
        expect($availment->loan_computation->is_income_sufficient)->toBeTrue();
    }

});

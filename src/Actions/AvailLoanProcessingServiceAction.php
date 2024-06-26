<?php

namespace Homeful\Availments\Actions;

use Homeful\Borrower\Exceptions\{MaximumBorrowingAgeBreached, MinimumBorrowingAgeNotMet};
use Brick\Math\Exception\{NumberFormatException, RoundingNecessaryException};
use Homeful\Availments\Interfaces\{BorrowerInterface, PropertyInterface};
use Brick\Money\Exception\UnknownCurrencyException;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Homeful\Availments\Models\Availment;
use Homeful\Borrower\Borrower;
use Homeful\Property\Property;
use Illuminate\Support\Arr;
use Homeful\Loan\Loan;

class AvailLoanProcessingServiceAction
{
    use AsAction;

    /**
     * @param BorrowerInterface $borrowerObject
     * @param PropertyInterface $propertyObject
     * @param array $adjustments
     * @param string|null $reference_code
     * @return Availment
     * @throws MaximumBorrowingAgeBreached
     * @throws MinimumBorrowingAgeNotMet
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Homeful\Loan\Exceptions\LoanExceedsNetTotalContractPriceException
     * @throws \Homeful\Property\Exceptions\MaximumContractPriceBreached
     * @throws \Homeful\Property\Exceptions\MinimumContractPriceBreached
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(BorrowerInterface $borrowerObject, PropertyInterface $propertyObject, array $adjustments, string $reference_code = null): Availment
    {
        $validated = Validator::make($adjustments, [
            'holding_fee' => ['nullable', 'integer', 'min:0', 'max:30000'],
            'total_contract_price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'percent_down_payment' => ['nullable', 'numeric', 'min:0', 'max:0.20'],
            'percent_miscellaneous_fees' => ['nullable', 'numeric', 'min:0', 'max:0.10'],
            'loan_interest' => ['nullable', 'numeric', 'min:0', 'max:0.16'],
            'loan_term' => ['nullable', 'integer', 'min:0', 'max:30'],
            'total_contract_price_balance_down_payment_term' => ['nullable', 'integer', 'min:0', 'max:12'],
            'low_cash_out_amount' => ['nullable', 'integer', 'min:0', 'max:50000'],
        ])->validate();

        $borrower = (new Borrower)
            ->setBirthdate($borrowerObject->getBirthdate())
            ->setRegional($borrowerObject->getRegional())
            ->addWages($borrowerObject->getWages());
        $property = (new Property)
            ->setTotalContractPrice($propertyObject->getTotalContractPrice())
            ->setAppraisedValue($propertyObject->getAppraisedValue());
        $loan = (new Loan)
            ->setBorrower($borrower)
            ->setProperty($property)
        ;
        $loan->setLoanAmount($loan->getNetTotalContractPrice());

        $product_sku = $propertyObject->getSKU();
        $holding_fee = Arr::get($validated, 'holding_fee', $propertyObject->getProcessingFee());
        $total_contract_price = Arr::get($validated, 'total_contract_price', $property->getTotalContractPrice());
        $percent_down_payment = Arr::get($validated, 'percent_down_payment', 5/100);
        $percent_miscellaneous_fees = Arr::get($validated, 'percent_miscellaneous_fees', 8.5/100);
        $loan_interest = Arr::get($validated, 'loan_interest', $loan->getAnnualInterestRate());
        $loan_term = Arr::get($validated, 'loan_term', 20);
        $total_contract_price_balance_down_payment_term = Arr::get($validated, 'total_contract_price_balance_down_payment_term', 12);
        $low_cash_out_amount = Arr::get($validated, 'low_cash_out_amount', 0);

        $attribs = [
            'product_sku' => $product_sku,
            'holding_fee' => $holding_fee,
            'total_contract_price' => $total_contract_price,
            'percent_miscellaneous_fees' => $percent_miscellaneous_fees,
            'percent_down_payment' => $percent_down_payment,
            'total_contract_price_balance_down_payment_term' => $total_contract_price_balance_down_payment_term,
            'loan_term' => $loan_term,
            'loan_interest' => $loan_interest,
            'low_cash_out_amount' => $low_cash_out_amount
        ];

        return tap(app(Availment::class)->create($attribs), function (Availment $availment) use ($loan) {
            $availment->loan_object = $loan;
            $availment->save();
        });
    }
}

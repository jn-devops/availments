<?php

namespace Homeful\Availments\Actions;

use Homeful\Availments\Models\Availment;
use Homeful\Loan\Exceptions\LoanExceedsLoanableValueException;
use Homeful\Property\Property;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Homeful\Availments\Interfaces\BorrowerInterface;
use Homeful\Availments\Interfaces\PropertyInterface;
use Homeful\Borrower\Borrower;
use Homeful\Borrower\Exceptions\MaximumBorrowingAgeBreached;
use Homeful\Borrower\Exceptions\MinimumBorrowingAgeNotMet;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Loan\Loan;
use Whitecube\Price\Price;
use Homeful\Loan\Data\LoanData;

class AvailLoanProcessingServiceAction
{
    use AsAction;

    /**
     * @throws MinimumBorrowingAgeNotMet
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     * @throws MaximumBorrowingAgeBreached
     */
    public function handle(BorrowerInterface $borrowerObject, PropertyInterface $propertyObject, Price $loanAmount = null, string $reference_code = null)
    {
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
            ->setLoanAmount($loanAmount ?: $property->getLoanableValue());

        $availment = new Availment(array_filter([
            'reference_code' => $reference_code,
            'borrower_mobile' => $borrowerObject->getMobile(),
            'product_sku' => $propertyObject->getSKU(),
            'processing_fee' => $propertyObject->getProcessingFee(),
            'loan_amount' => $loan->getLoanAmount(),
            'down_payment_monthly_amortization' => 0,
            'down_payment_months_to_pay' => 0,
            'balance_payment_monthly_amortization' => $loan->getMonthlyAmortizationAmount(),
            'balance_payment_months_to_pay' => $loan->getMaximumMonthsToPay(),
            'balance_payment_annual_interest' => $loan->getAnnualInterestRate(),
            'seller_commission_code' => $borrowerObject->getSellerCommissionCode(),
        ]));

        $availment->loan_computation = LoanData::fromObject($loan);
        $availment->save();

        return $availment;
    }
}

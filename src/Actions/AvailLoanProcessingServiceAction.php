<?php

namespace Homeful\Availments\Actions;

use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Homeful\Availments\Models\Availment;
use Homeful\Borrower\Borrower;
use Homeful\Borrower\Exceptions\MaximumBorrowingAgeBreached;
use Homeful\Borrower\Exceptions\MinimumBorrowingAgeNotMet;
use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Common\Interfaces\PropertyInterface;
use Homeful\Loan\Loan;
use Homeful\Property\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class AvailLoanProcessingServiceAction
{
    use AsAction;

    protected static ?float $percent_down_payment = null;

    protected static ?float $percent_miscellaneous_fees = null;

    protected static ?int $loan_term = null;

    protected static ?int $total_contract_price_balance_down_payment_term = null;

    protected static ?float $loan_interest = null;

    protected static ?float $low_cash_out_amount = null;

    protected Loan $loan;

    /**
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
    public function handle(BorrowerInterface $borrowerObject, PropertyInterface $propertyObject, array $adjustments, ?string $reference_code = null): Availment
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
        $this->loan = (new Loan)
            ->setBorrower($borrower)
            ->setProperty($property);
        $this->loan->setLoanAmount($this->loan->getNetTotalContractPrice());

        $product_sku = $propertyObject->getSKU();
        $holding_fee = Arr::get($validated, 'holding_fee', $propertyObject->getProcessingFee());
        $total_contract_price = Arr::get($validated, 'total_contract_price', $property->getTotalContractPrice());
        $percent_down_payment = Arr::get($validated, 'percent_down_payment', $this->getPercentDownPayment());
        $percent_miscellaneous_fees = Arr::get($validated, 'percent_miscellaneous_fees', $this->getPercentMiscellaneousFees());
        $loan_interest = Arr::get($validated, 'loan_interest', $this->getLoanInterest());
        $loan_term = Arr::get($validated, 'loan_term', $this->getLoanTerm());
        $total_contract_price_balance_down_payment_term = Arr::get($validated, 'total_contract_price_balance_down_payment_term', $this->getTotalContractPriceBalanceDownPaymentTerm());
        $low_cash_out_amount = Arr::get($validated, 'low_cash_out_amount', $this->getLowCashOutAmount());

        $attribs = [
            'product_sku' => $product_sku,
            'holding_fee' => $holding_fee,
            'total_contract_price' => $total_contract_price,
            'percent_miscellaneous_fees' => $percent_miscellaneous_fees,
            'percent_down_payment' => $percent_down_payment,
            'total_contract_price_balance_down_payment_term' => $total_contract_price_balance_down_payment_term,
            'loan_term' => $loan_term,
            'loan_interest' => $loan_interest,
            'low_cash_out_amount' => $low_cash_out_amount,
        ];

        $availment = app(Availment::class)->create($attribs);
        if ($availment instanceof Availment) {
            $availment->loan_object = $this->loan;
            $availment->save();
        }
        $this->resetProperties();

        return $availment;
    }

    /**
     * @return $this
     */
    public function setPercentDownPayment(float $percent_down_payment): self
    {
        self::$percent_down_payment = $percent_down_payment;

        return $this;
    }

    /**
     * @return float
     */
    public function getPercentDownPayment(): float
    {
        return self::$percent_down_payment ?? config('availments.default_percent_down_payment');
    }

    /**
     * @return $this
     */
    public function setPercentMiscellaneousFees(float $percent_miscellaneous_fees): self
    {
        self::$percent_miscellaneous_fees = $percent_miscellaneous_fees;

        return $this;
    }

    /**
     * @return float
     */
    public function getPercentMiscellaneousFees(): float
    {
        return self::$percent_miscellaneous_fees ?? config('availments.default_percent_miscellaneous_fees');
    }

    /**
     * @return $this
     */
    public function setLoanTerm(int $loan_term): self
    {
        self::$loan_term = $loan_term;

        return $this;
    }

    /**
     * @return int
     */
    public function getLoanTerm(): int
    {
        return self::$loan_term ?? config('availments.default_loan_term');
    }

    /**
     * @return $this
     */
    public function setTotalContractPriceBalanceDownPaymentTerm(int $total_contract_price_balance_down_payment_term): self
    {
        self::$total_contract_price_balance_down_payment_term = $total_contract_price_balance_down_payment_term;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalContractPriceBalanceDownPaymentTerm(): int
    {
        return self::$total_contract_price_balance_down_payment_term ?? config('availments.default_total_contract_price_balance_down_payment_term');
    }

    /**
     * @param float $loan_interest
     * @return $this
     */
    public function setLoanInterest(float $loan_interest): self
    {
        self::$loan_interest = $loan_interest;

        return $this;
    }

    /**
     * @return float
     */
    public function getLowCashOutAmount(): float
    {
        return self::$low_cash_out_amount ?? 0.0;
    }

    /**
     * @param float $low_cash_out_amount
     * @return $this
     */
    public function setLowCashOutAmount(float $low_cash_out_amount): self
    {
        self::$low_cash_out_amount = $low_cash_out_amount;

        return $this;
    }

    /**
     * @return float
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    protected function getLoanInterest(): float
    {
        return self::$loan_interest ?? $this->loan->getAnnualInterestRate();
    }

    /**
     * @return void
     */
    public function resetProperties(): void
    {
        self::$percent_down_payment = null;
        self::$percent_miscellaneous_fees = null;
        self::$loan_term = null;
        self::$total_contract_price_balance_down_payment_term = null;
        self::$loan_interest = null;
        self::$low_cash_out_amount = null;
    }
}

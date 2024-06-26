<?php

namespace Homeful\Availments\Traits;

use Brick\Math\RoundingMode;
use Jarouche\Financial\PMT;
use Whitecube\Price\Price;
use Brick\Money\Money;

trait UpdatingAvailment
{
    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     */
    public function updatingMiscellaneousFees(): self
    {
        $percent_miscellaneous_fees = $this->percent_miscellaneous_fees;
        if (($tcp = $this->total_contract_price->inclusive()) instanceof Money)
            $this->miscellaneous_fees = new Price($tcp->multipliedBy($percent_miscellaneous_fees, roundingMode: RoundingMode::CEILING));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     */
    public function updatingTotalContractPriceDownPaymentAmount(): self
    {
        $percent_down_payment = $this->percent_down_payment;
        if (($tcp = $this->total_contract_price->inclusive()) instanceof Money)
            $this->total_contract_price_down_payment_amount = new Price($tcp->multipliedBy($percent_down_payment, roundingMode: RoundingMode::CEILING));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     */
    public function updatingTotalContractPriceBalanceDownPaymentAmount(): self
    {
        $tcp_down_payment_amount = $this->total_contract_price_down_payment_amount->inclusive();
        $holding_fee = $this->holding_fee->inclusive();
        $this->total_contract_price_balance_down_payment_amount = new Price($tcp_down_payment_amount->minus($holding_fee, roundingMode: RoundingMode::CEILING));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     */
    public function updatingTotalContractPriceBalanceDownPaymentAmortizationAmount(): self
    {
        $tcp_balance_down_payment_amount = $this->total_contract_price_balance_down_payment_amount->inclusive();
        $tcp_balance_down_payment_term = $this->total_contract_price_balance_down_payment_term;
        $this->total_contract_price_balance_down_payment_amortization_amount = new Price($tcp_balance_down_payment_amount->dividedBy($tcp_balance_down_payment_term, roundingMode: RoundingMode::CEILING));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     */
    public function updatingMiscellaneousFeesDownPaymentAmount(): self
    {
        $percent_down_payment = $this->percent_down_payment;
        $miscellaneous_fees = $this->miscellaneous_fees->inclusive();
        $this->miscellaneous_fees_down_payment_amount = new Price($miscellaneous_fees->multipliedBy($percent_down_payment, roundingMode: RoundingMode::CEILING));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function updatingLoanAmount(): self
    {
        if ($this->isPromotional) {
            $tcp_balance_payment_amount = $this->total_contract_price_balance_payment_amount->inclusive();
            $miscellaneous_fees = $this->miscellaneous_fees->inclusive();
            $this->loan_amount = new Price($tcp_balance_payment_amount->plus($miscellaneous_fees, roundingMode: RoundingMode::CEILING));
        }
        else {
            $tcp_balance_payment_amount = $this->total_contract_price_balance_payment_amount->inclusive();
            $mf_balance_payment_amount = $this->miscellaneous_fees_balance_payment_amount->inclusive();
            $this->loan_amount = new Price($tcp_balance_payment_amount->plus($mf_balance_payment_amount, roundingMode: RoundingMode::CEILING));
        }

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function updatingLoanAmortizationAmount(): self
    {
        $interest_rate = $this->loan_interest/12;
        $months_to_pay = $this->loan_term * 12;

        $obj = new PMT($interest_rate, $months_to_pay, $this->loan_amount->inclusive()->getAmount()->toFloat());
        $float = round($obj->evaluate());
        $this->loan_amortization_amount = new Price(Money::of($float, 'PHP'));

        return $this;
    }

    /**
     * @return UpdatingAvailment|\Homeful\Availments\Models\Availment
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function updatingBalanceCashOutAmount(): self
    {
        $amount = Money::of(0, 'PHP');
        if ($this->low_cash_out_amount instanceof Price) {
            $low_cash_out = $this->low_cash_out_amount->inclusive();
            if ($low_cash_out->compareTo(0) == 1) {
                $holding_fee = $this->holding_fee->inclusive();
                $amount = $low_cash_out->minus($holding_fee);
            }
        }
        $this->balance_cash_out_amount = new Price($amount);

        return $this;
    }
}

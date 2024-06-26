<?php

namespace Homeful\Availments\Data;

use Homeful\Availments\Models\Availment;
use Homeful\Loan\Data\LoanData;
use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class AvailmentData extends Data
{
    /**
     * @param  LoanData  $loan_data
     *
     * @method self fromModel()
     */
    public function __construct(
        public string $product_sku,
        public float $holding_fee,
        public float $total_contract_price,
        public float $percent_miscellaneous_fees,
        public float $miscellaneous_fees,
        public float $net_total_contract_price,
        public float $percent_down_payment,
        public float $total_contract_price_down_payment_amount,
        public float $total_contract_price_balance_down_payment_amount,
        public int $total_contract_price_balance_down_payment_term,
        public float $total_contract_price_balance_down_payment_amortization_amount,
        public float $miscellaneous_fees_down_payment_amount,
        public float $percent_balance_payment,
        public float $total_contract_price_balance_payment_amount,
        public float $miscellaneous_fees_balance_payment_amount,
        public float $loan_amount,
        public int $loan_term,
        public float $loan_interest,
        public float $loan_amortization_amount,
        public float $low_cash_out_amount,
        public float $balance_cash_out_amount,
        public LoanData|Optional $loan_data
    ) {}

    /**
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public static function fromModel(Availment $model): self
    {
        return new self(
            product_sku: $model->product_sku,
            holding_fee: $model->holding_fee->inclusive()->getAmount()->toFloat(),
            total_contract_price: $model->total_contract_price->inclusive()->getAmount()->toFloat(),
            percent_miscellaneous_fees: $model->percent_miscellaneous_fees,
            miscellaneous_fees: $model->miscellaneous_fees->inclusive()->getAmount()->toFloat(),
            net_total_contract_price: $model->net_total_contract_price->inclusive()->getAmount()->toFloat(),
            percent_down_payment: $model->percent_down_payment,
            total_contract_price_down_payment_amount: $model->total_contract_price_down_payment_amount->inclusive()->getAmount()->toFloat(),
            total_contract_price_balance_down_payment_amount: $model->total_contract_price_balance_down_payment_amount->inclusive()->getAmount()->toFloat(),
            total_contract_price_balance_down_payment_term: $model->total_contract_price_balance_down_payment_term,
            total_contract_price_balance_down_payment_amortization_amount: $model->total_contract_price_balance_down_payment_amortization_amount->inclusive()->getAmount()->toFloat(),
            miscellaneous_fees_down_payment_amount: $model->miscellaneous_fees_down_payment_amount->inclusive()->getAmount()->toFloat(),
            percent_balance_payment: $model->percent_balance_payment,
            total_contract_price_balance_payment_amount: $model->total_contract_price_balance_payment_amount->inclusive()->getAmount()->toFloat(),
            miscellaneous_fees_balance_payment_amount: $model->miscellaneous_fees_balance_payment_amount->inclusive()->getAmount()->toFloat(),
            loan_amount: $model->loan_amount->inclusive()->getAmount()->toFloat(),
            loan_term: $model->loan_term,
            loan_interest: $model->loan_interest,
            loan_amortization_amount: $model->loan_amortization_amount->inclusive()->getAmount()->toFloat(),
            low_cash_out_amount: $model->low_cash_out_amount->inclusive()->getAmount()->toFloat(),
            balance_cash_out_amount: $model->balance_cash_out_amount->inclusive()->getAmount()->toFloat(),
            loan_data: optional($model->loan_data)
        );
    }
}

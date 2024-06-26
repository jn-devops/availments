<?php

namespace Homeful\Availments\Models;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Homeful\Availments\Data\AvailmentData;
use Homeful\Availments\Traits\UpdatingAvailment;
use Homeful\Common\Traits\HasMeta;
use Homeful\Common\Traits\HasPackageFactory as HasFactory;
use Homeful\Loan\Data\LoanData;
use Homeful\Loan\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Whitecube\Price\Price;

/**
 * Class Availment
 *
 * @property int $id
 * @property string $product_sku
 * @property Price $holding_fee
 * @property Price $total_contract_price
 * @property float $percent_miscellaneous_fees
 * @property Price $miscellaneous_fees
 * @property Price $net_total_contract_price
 * @property float $percent_down_payment
 * @property Price $total_contract_price_down_payment_amount
 * @property Price $total_contract_price_balance_down_payment_amount
 * @property int $total_contract_price_balance_down_payment_term
 * @property Price $total_contract_price_balance_down_payment_amortization_amount
 * @property Price $miscellaneous_fees_down_payment_amount
 * @property float $percent_balance_payment
 * @property Price $total_contract_price_balance_payment_amount
 * @property Price $miscellaneous_fees_balance_payment_amount
 * @property Price $loan_amount
 * @property int $loan_term
 * @property float $loan_interest
 * @property Price $loan_amortization_amount
 * @property Price $low_cash_out_amount
 * @property Price $balance_cash_out_amount
 * @property Loan $loan_object
 * @property LoanData $loan_data
 *
 * @method int getKey()
 * @method Builder withMeta(...$args)
 * @method Availment updatingMiscellaneousFees()
 * @method Availment updatingTotalContractPriceDownPaymentAmount()
 * @method Availment updatingTotalContractPriceBalanceDownPaymentAmount()
 * @method Availment updatingTotalContractPriceBalanceDownPaymentAmortizationAmount()
 * @method Availment updatingMiscellaneousFeesDownPaymentAmount()
 * @method Availment updatingLoanAmount()
 * @method Availment updatingLoanAmortizationAmount()
 * @method Availment updatingBalanceCashOutAmount()
 */
class Availment extends Model
{
    use HasFactory;
    use HasMeta;
    use UpdatingAvailment;

    protected $fillable = [
        'product_sku',
        'holding_fee',
        'total_contract_price',
        'percent_miscellaneous_fees',
        'percent_down_payment',
        'total_contract_price_balance_down_payment_term',
        'loan_term',
        'loan_interest',
        'low_cash_out_amount',
    ];

    //    protected $appends = [
    //        'loan_data'
    //    ];

    /**
     * This is the same as processing fee.
     */
    protected function HoldingFee(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn (Price|int $value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    protected function TotalContractPrice(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn (Price|int $value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setPercentMiscellaneousFeesAttribute(float $value): self
    {
        if ($value > 10 / 100) {
            throw new \Exception('Maximum percent miscellaneous fees breached. Please contact the software developer.');
        }
        if ($value < 0) {
            throw new \Exception('Minimum percent miscellaneous fees breached');
        }

        $this->getAttribute('meta')->set('miscellaneous_fees.percent', $value);

        return $this;
    }

    public function getPercentMiscellaneousFeesAttribute(): float
    {
        return $this->getAttribute('meta')->get('miscellaneous_fees.percent', 0.0);
    }

    protected function MiscellaneousFees(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    public function getNetTotalContractPriceAttribute(): Price
    {
        return new Price($this->total_contract_price->inclusive()->plus($this->miscellaneous_fees->inclusive()));
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setPercentDownPaymentAttribute(float $value): self
    {
        if ($value > 20 / 100) {
            throw new \Exception('Maximum percent down payment breached. Please contact the software developer.');
        }
        if ($value < 0) {
            throw new \Exception('Minimum percent down payment breached');
        }
        $this->getAttribute('meta')->set('down_payment.percent', $value);

        return $this;
    }

    public function getPercentDownPaymentAttribute(): float
    {
        return $this->getAttribute('meta')->get('down_payment.percent', (float) config('availments.default_percent_down_payment'));
    }

    protected function TotalContractPriceDownPaymentAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    protected function TotalContractPriceBalanceDownPaymentAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setTotalContractPriceBalanceDownPaymentTermAttribute(int $value): self
    {
        if ($value > 24) {
            throw new \Exception('Maximum balance down payment term breached. Please contact the software developer.');
        }
        if ($value < 0) {
            throw new \Exception('Minimum balance down payment term breached');
        }

        $this->getAttribute('meta')->set('total_contract_price_balance_down_payment.term', $value);

        return $this;
    }

    public function getTotalContractPriceBalanceDownPaymentTermAttribute(): int
    {
        return $this->getAttribute('meta')->get('total_contract_price_balance_down_payment.term', (int) config('availments.default_total_contract_price_balance_down_payment_term'));
    }

    protected function TotalContractPriceBalanceDownPaymentAmortizationAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    protected function MiscellaneousFeesDownPaymentAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    public function getPercentBalancePaymentAttribute(): float
    {
        return 1 - $this->percent_down_payment;
    }

    public function getTotalContractPriceBalancePaymentAmountAttribute(): Price
    {
        return new Price($this->total_contract_price->inclusive()->multipliedBy($this->percent_balance_payment, roundingMode: RoundingMode::CEILING));
    }

    public function getMiscellaneousFeesBalancePaymentAmountAttribute(): Price
    {
        return new Price($this->miscellaneous_fees->inclusive()->multipliedBy($this->percent_balance_payment, roundingMode: RoundingMode::CEILING));
    }

    protected function LoanAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    protected function LoanAmortizationAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    /**
     * @return $this
     *
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function setLowCashOutAmountAttribute(Price|float $value): self
    {
        $amount = $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt();

        if ($amount instanceof Money) {
            if ($amount->compareTo($this->holding_fee) == -1) {
                throw new \Exception('Minimum low cash amount breached. Please contact the software developer.');
            }

        }

        $this->getAttribute('meta')->set('low_cash_out.amount', $amount);

        return $this;
    }

    /**
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getLowCashOutAmountAttribute(): Price
    {
        $amount = $this->getAttribute('meta')->get('low_cash_out.amount', 0);

        return new Price(Money::ofMinor($amount, 'PHP'));
    }

    protected function BalanceCashOutAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt() : Money::of($value, 'PHP')->getMinorAmount()->toInt()
        );
    }

    /**
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function getIsPromotionalAttribute(): bool
    {
        return $this->low_cash_out_amount->inclusive()->compareTo($this->holding_fee->inclusive()) == 1;
    }

    public function setLoanObjectAttribute(Loan $value): self
    {
        $this->getAttribute('meta')->set('loan.object', serialize($value));

        return $this;
    }

    public function getLoanObjectAttribute(): ?Loan
    {
        $serialized = $this->getAttribute('meta')->get('loan.object');

        return $serialized ? unserialize($serialized) : null;
    }

    public function getLoanDataAttribute(): ?LoanData
    {
        return $this->loan_object instanceof Loan ? LoanData::fromObject($this->loan_object) : null;
    }

    public function toData(): AvailmentData
    {
        return AvailmentData::fromModel($this);
    }
}

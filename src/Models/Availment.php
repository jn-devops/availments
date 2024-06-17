<?php

namespace Homeful\Availments\Models;

use Homeful\Common\Traits\HasPackageFactory as HasFactory;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Homeful\Common\Traits\HasMeta;
use Homeful\Loan\Data\LoanData;
use Whitecube\Price\Price;
use Brick\Money\Money;

/**
 * Class Availment
 *
 * @property int         $id
 * @property string      $reference_code
 * @property PhoneNumber $borrower_mobile,
 * @property string      $product_sku
 * @property Price       $processing_fee
 * @property Price       $loan_amount
 * @property Price       $down_payment_monthly_amortization
 * @property int         $down_payment_months_to_pay
 * @property Price       $balance_payment_monthly_amortization
 * @property int         $balance_payment_months_to_pay
 * @property float       $balance_payment_annual_interest
 * @property string      $seller_commission_code
 * @property LoanData    $loan_computation
 *
 * @method int           getKey()
 * @method Builder       withMeta(...$args)
 */
class Availment extends Model
{
    use HasFactory;
    use HasMeta;

    protected $fillable = [
        'borrower_mobile',
        'product_sku',
        'processing_fee',
        'loan_amount',
        'down_payment_monthly_amortization',
        'down_payment_months_to_pay',
        'balance_payment_monthly_amortization',
        'balance_payment_months_to_pay',
        'balance_payment_annual_interest',
        'seller_commission_code',
    ];

    protected $casts = [
        'borrower_mobile' => RawPhoneNumberCast::class.':PH',
    ];

    protected function ProcessingFee(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt(): $value * 100
        );
    }

    protected function LoanAmount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt(): $value * 100
        );
    }

    protected function DownPaymentMonthlyAmortization(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt(): $value * 100
        );
    }

    protected function BalancePaymentMonthlyAmortization(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Price(Money::ofMinor($value, 'PHP')),
            set: fn ($value) => $value instanceof Price ? $value->inclusive()->getMinorAmount()->toInt(): $value * 100
        );
    }

    public function setReferenceCodeAttribute(string $value): self
    {
        $this->getAttribute('meta')->set('reference.code', $value);

        return $this;
    }

    public function getReferenceCodeAttribute(): ?string
    {
        return $this->getAttribute('meta')->get('reference.code');
    }

    public function setSellerCommissionCodeAttribute(string $value): self
    {
        $this->getAttribute('meta')->set('seller.commission_code', $value);

        return $this;
    }

    public function getSellerCommissionCodeAttribute(): ?string
    {
        return $this->getAttribute('meta')->get('seller.commission_code');
    }

    public function setLoanComputationAttribute(LoanData $value): self
    {
        $this->getAttribute('meta')->set('loan.computation', $value->toArray());

        return $this;
    }

    public function getLoanComputationAttribute(): ?LoanData
    {
        return optional($this->getAttribute('meta')->get('loan.computation'), function ($array) {
            return LoanData::from($array);
        });
    }
}

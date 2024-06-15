<?php

namespace Homeful\Availments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Whitecube\Price\Price;
use Brick\Money\Money;

/**
 * Class Availment
 *
 * @property int    $id
 * @property string $reference_code
 * @property string $sku
 * @property Price  $processing_fee
 * @property Price  $loan_amount
 * @property Price  $down_payment_monthly_amortization
 * @property int    $down_payment_months_to_pay
 * @property Price  $balance_payment_monthly_amortization
 * @property int    $balance_payment_months_to_pay
 * @property float  $balance_payment_annual_interest
 * @property string $seller_commission_code
 * @property array  $loan_computation
 *
 * @method int getKey()
 */
class Availment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'sku',
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
        'loan_computation' => 'array'
    ];
    protected static function newFactory()
    {
        $modelName = static::class;
        $path = 'Homeful\\Availments\\Database\\Factories\\'.class_basename($modelName).'Factory';

        return app($path)->new();
    }

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
}

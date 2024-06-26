<?php

namespace Homeful\Availments\Interfaces;

use Brick\Money\Money;
use Illuminate\Support\Carbon;
use Propaganistas\LaravelPhone\PhoneNumber;

interface BorrowerInterface
{
    public function getBirthdate(): Carbon;

    public function getWages(): Money|float;

    public function getRegional(): bool;

    public function getMobile(): PhoneNumber;

    public function getSellerCommissionCode(): string;
}

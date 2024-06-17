<?php

namespace Homeful\Availments\Interfaces;

use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Carbon;
use Brick\Money\Money;

interface BorrowerInterface
{
    public function getBirthdate(): Carbon;

    public function getWages(): Money|float;

    public function getRegional(): bool;

    public function getMobile(): PhoneNumber;

    public function getSellerCommissionCode(): string;
}

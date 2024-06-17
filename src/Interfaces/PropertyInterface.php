<?php

namespace Homeful\Availments\Interfaces;

use Whitecube\Price\Price;

interface PropertyInterface
{
    public function getSKU(): string;

    public function getProcessingFee(): Price;

    public function getTotalContractPrice(): Price;

    public function getAppraisedValue(): Price;
}

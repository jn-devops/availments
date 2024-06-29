<?php

namespace Homeful\Availments\Observers;

use Homeful\Availments\Models\Availment;

class AvailmentObserver
{
    /**
     * Handle the Availment "creating" event.
     */
    public function creating(Availment $availment): void
    {
        $this->updatingAvailment($availment);
    }

    /**
     * Handle the Availment "created" event.
     */
    public function created(Availment $availment): void {}

    /**
     * Handle the Availment "updated" event.
     */
    public function updating(Availment $availment): void
    {
        $this->updatingAvailment($availment);
    }

    /**
     * Handle the Availment "deleted" event.
     */
    public function deleted(Availment $availment): void
    {
        //
    }

    /**
     * Handle the Availment "restored" event.
     */
    public function restored(Availment $availment): void
    {
        //
    }

    /**
     * Handle the Availment "force deleted" event.
     */
    public function forceDeleted(Availment $availment): void
    {
        //
    }

    protected function updatingAvailment(Availment $availment): void
    {
        $availment
            ->updatingMiscellaneousFees()
            ->updatingTotalContractPriceDownPaymentAmount()
            ->updatingTotalContractPriceBalanceDownPaymentAmount()
            ->updatingTotalContractPriceBalanceDownPaymentAmortizationAmount()
            ->updatingMiscellaneousFeesDownPaymentAmount()
            ->updatingLoanAmount()
            ->updatingLoanAmortizationAmount()
            ->updatingBalanceCashOutAmount();
    }
}

<?php

namespace Homeful\Availments\Providers;

use Homeful\Availments\Models\Availment;
use Homeful\Availments\Observers\AvailmentObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Availment::observe(AvailmentObserver::class);
    }
}

<?php

namespace Homeful\Availments\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Homeful\Availments\Observers\AvailmentObserver;
use Homeful\Availments\Models\Availment;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Availment::observe(AvailmentObserver::class);
    }
}

<?php

namespace Homeful\Availments\Commands;

use Illuminate\Console\Command;

class AvailmentsCommand extends Command
{
    public $signature = 'availments';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

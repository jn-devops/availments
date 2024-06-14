<?php

namespace Homeful\Availments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Homeful\Availments\Availments
 */
class Availments extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Homeful\Availments\Availments::class;
    }
}

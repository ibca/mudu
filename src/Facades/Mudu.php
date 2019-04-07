<?php

namespace Ibca\Mudu\Facades;

use Illuminate\Support\Facades\Facade;

/**
 */
class Mudu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mudu';
    }
}

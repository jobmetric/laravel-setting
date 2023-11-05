<?php

namespace JobMetric\EnvModifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Setting\Setting
 */
class Setting extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Setting';
    }
}

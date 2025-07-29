<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Setting\SettingType
 *
 * @method static \JobMetric\Setting\SettingType define(string $type)
 * @method static \JobMetric\Setting\SettingType type(string $type)
 * @method static array get()
 * @method static array getTypes()
 * @method static bool hasType(string $type)
 * @method static void ensureTypeExists(string $type)
 */
class SettingType extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'SettingType';
    }
}

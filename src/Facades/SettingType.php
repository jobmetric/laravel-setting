<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Setting\SettingType
 *
 * @method static \JobMetric\Setting\SettingType define(string $type)
 * @method static \JobMetric\Setting\SettingType type(string $type)
 * @method static array getTypes()
 * @method static bool hasType(string $type)
 * @method static void checkType(string|null $type)
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
        return \JobMetric\Setting\SettingType::class;
    }
}

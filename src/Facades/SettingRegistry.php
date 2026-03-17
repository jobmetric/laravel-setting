<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Setting\Support\SettingRegistry
 *
 * @method static \JobMetric\Setting\Support\SettingRegistry register(string $class)
 * @method static \JobMetric\Setting\Support\SettingRegistry unregister(string $class)
 * @method static bool has(string $class)
 * @method static array<int, string> all()
 * @method static array<string, mixed>|null resolveSpec(string $class)
 * @method static \JobMetric\Setting\Support\SettingRegistry clear()
 * @method static \JobMetric\Setting\Support\SettingRegistry discover()
 */
class SettingRegistry extends Facade
{
    /**
     * Container binding key for this facade.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'SettingRegistry';
    }
}

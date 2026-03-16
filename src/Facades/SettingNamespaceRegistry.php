<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Setting\Support\SettingNamespaceRegistry
 *
 * @method static \JobMetric\Setting\Support\SettingNamespaceRegistry register(string $namespace)
 * @method static \JobMetric\Setting\Support\SettingNamespaceRegistry unregister(string $namespace)
 * @method static bool has(string $namespace)
 * @method static array<int, string> all()
 * @method static \JobMetric\Setting\Support\SettingNamespaceRegistry clear()
 */
class SettingNamespaceRegistry extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     *
     * This accessor must match the binding defined in the package service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'SettingNamespaceRegistry';
    }
}

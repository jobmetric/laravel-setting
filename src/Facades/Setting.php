<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Setting\Setting
 *
 * @method static void store(string $code, array $object, bool $has_event = true)
 * @method static void forget(string $code, bool $has_event = true)
 * @method static void setAll(mixed $data)
 * @method static void set(string $string, mixed $item)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static array code(string $code)
 * @method static bool has(string $key)
 * @method static void unset(string $key)
 * @method static array all()
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

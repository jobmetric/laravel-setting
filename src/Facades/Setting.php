<?php

namespace JobMetric\Setting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Setting\Services\Setting
 *
 * @method static array<string, mixed> dispatch(string $class, array|null $input = null, bool $has_event = true)
 * @method static void dispatchByForm(string $form, array $object, bool $has_event = true)
 * @method static void forget(string $form, bool $has_event = true)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed getFromClass(string $class, string|null $fieldKey = null, mixed $default = null)
 * @method static void invalidateCache()
 * @method static bool exists(string $form)
 * @method static array<string, mixed> getMany(array $keys, mixed $default = null)
 * @method static array<string, mixed> formFromClass(string $class)
 *
 * @see \JobMetric\Setting\Exceptions\SettingClassInvalidException
 * @method static array<string, mixed> form(string $form)
 * @method static bool has(string $key)
 * @method static array<string, mixed> all()
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

<?php

use JobMetric\Setting\Facades\Setting;

if (!function_exists('dispatchSetting')) {
    /**
     * Dispatch by form name and raw object; no validation.
     *
     * @param string $form Form name (application_key).
     * @param array<string, mixed> $object
     * @param bool $has_event
     * @return void
     */
    function dispatchSetting(string $form, array $object, bool $has_event = true): void
    {
        Setting::dispatchByForm($form, $object, $has_event);
    }
}

if (!function_exists('dispatchSettingFromClass')) {
    /**
     * Dispatch by setting class: validate with dto and store. Uses request()->all() when $input is null.
     *
     * @param class-string<\JobMetric\Setting\Contracts\AbstractSetting> $class
     * @param array<string, mixed>|null $input
     * @param bool $has_event
     * @return array<string, mixed>
     */
    function dispatchSettingFromClass(string $class, ?array $input = null, bool $has_event = true): array
    {
        return Setting::dispatch($class, $input, $has_event);
    }
}

if (!function_exists('forgetSetting')) {
    /**
     * forget setting
     *
     * @param string $form
     * @param bool $has_event
     *
     * @return void
     */
    function forgetSetting(string $form, bool $has_event = true): void
    {
        Setting::forget($form, $has_event);
    }
}

if (!function_exists('getSetting')) {
    /**
     * get setting
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function getSetting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('formSetting')) {
    /**
     * form setting
     *
     * @param string $form
     *
     * @return array
     */
    function formSetting(string $form): array
    {
        return Setting::form($form);
    }
}

if (!function_exists('hasSetting')) {
    /**
     * has setting
     *
     * @param string $key
     *
     * @return bool
     */
    function hasSetting(string $key): bool
    {
        return Setting::has($key);
    }
}

if (!function_exists('getSettingFromClass')) {
    /**
     * Get one field value by setting class and field key; or entire form when $fieldKey is null.
     *
     * @param class-string<\JobMetric\Setting\Contracts\AbstractSetting> $class
     * @param string|null $fieldKey Null to return full form array.
     * @param mixed $default
     * @return mixed
     */
    function getSettingFromClass(string $class, ?string $fieldKey = null, mixed $default = null): mixed
    {
        return Setting::getFromClass($class, $fieldKey, $default);
    }
}

if (!function_exists('invalidateSettingCache')) {
    /**
     * Invalidate settings cache.
     *
     * @return void
     */
    function invalidateSettingCache(): void
    {
        Setting::invalidateCache();
    }
}

if (!function_exists('existsSetting')) {
    /**
     * Check whether at least one record exists for the given form.
     *
     * @param string $form
     * @return bool
     */
    function existsSetting(string $form): bool
    {
        return Setting::exists($form);
    }
}

if (!function_exists('getSettingsMany')) {
    /**
     * Get multiple keys at once; each missing key gets $default.
     *
     * @param array<int, string> $keys
     * @param mixed $default
     * @return array<string, mixed>
     */
    function getSettingsMany(array $keys, mixed $default = null): array
    {
        return Setting::getMany($keys, $default);
    }
}

if (!function_exists('formSettingFromClass')) {
    /**
     * Get all settings for a form by setting class.
     *
     * @param class-string<\JobMetric\Setting\Contracts\AbstractSetting> $class
     * @return array<string, mixed>
     */
    function formSettingFromClass(string $class): array
    {
        return Setting::formFromClass($class);
    }
}

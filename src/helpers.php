<?php

use JobMetric\Setting\Facades\Setting;

if (!function_exists('dispatchSetting')) {
    /**
     * dispatch setting
     *
     * @param string $code
     * @param array $object
     * @param bool $has_event
     *
     * @return void
     */
    function dispatchSetting(string $code, array $object, bool $has_event = true): void
    {
        Setting::dispatch($code, $object, $has_event);
    }
}

if (!function_exists('forgetSetting')) {
    /**
     * forget setting
     *
     * @param string $code
     * @param bool $has_event
     *
     * @return void
     */
    function forgetSetting(string $code, bool $has_event = true): void
    {
        Setting::forget($code, $has_event);
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

if (!function_exists('codeSetting')) {
    /**
     * code setting
     *
     * @param string $code
     *
     * @return array
     */
    function codeSetting(string $code): array
    {
        return Setting::code($code);
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

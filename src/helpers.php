<?php

use JobMetric\Setting\Facades\Setting;

if (!function_exists('dispatchSetting')) {
    /**
     * dispatch setting
     *
     * @param string $form
     * @param array $object
     * @param bool $has_event
     *
     * @return void
     */
    function dispatchSetting(string $form, array $object, bool $has_event = true): void
    {
        Setting::dispatch($form, $object, $has_event);
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

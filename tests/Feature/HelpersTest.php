<?php

namespace JobMetric\Setting\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use JobMetric\Setting\Exceptions\SettingClassInvalidException;
use JobMetric\Setting\Tests\Stubs\ConfigSettingStub;
use JobMetric\Setting\Tests\TestCase;
use stdClass;

/**
 * Feature tests for global helper functions defined in helpers.php.
 *
 * Purpose: Ensure each helper correctly delegates to the Setting facade and behaves as documented.
 * Tests: dispatchSetting, forgetSetting, getSetting, formSetting, hasSetting, getSettingFromClass,
 * invalidateSettingCache, existsSetting, getSettingsMany, formSettingFromClass; and that invalid
 * class triggers SettingClassInvalidException.
 */
class HelpersTest extends TestCase
{
    /**
     * dispatchSetting stores data via dispatchByForm and value is readable with getSetting.
     */
    public function test_dispatchSetting_calls_dispatchByForm(): void
    {
        dispatchSetting('app_config', [
            'app_config_foo' => 'bar',
        ]);

        $this->assertSame('bar', getSetting('app_config_foo'));
    }

    /**
     * forgetSetting removes all keys for the form; existsSetting returns false afterward.
     */
    public function test_forgetSetting_removes_form(): void
    {
        dispatchSetting('app_config', [
            'app_config_x' => 'y',
        ]);
        $this->assertTrue(hasSetting('app_config_x'));

        forgetSetting('app_config');

        $this->assertFalse(existsSetting('app_config'));
    }

    /**
     * getSetting returns stored value or the given default for missing key.
     */
    public function test_getSetting_returns_value_or_default(): void
    {
        dispatchSetting('app_config', [
            'app_config_a' => 'b',
        ]);
        $this->assertSame('b', getSetting('app_config_a'));
        $this->assertSame('default', getSetting('missing_key', 'default'));
    }

    /**
     * formSetting returns only keys that start with the given form prefix.
     */
    public function test_formSetting_returns_form_keys(): void
    {
        dispatchSetting('app_config', [
            'app_config_x' => '1',
            'app_config_y' => '2',
        ]);
        dispatchSetting('app_other', [
            'app_other_z' => '3',
        ]);

        $form = formSetting('app_config');

        $this->assertArrayHasKey('app_config_x', $form);
        $this->assertArrayHasKey('app_config_y', $form);
        $this->assertArrayNotHasKey('app_other_z', $form);
    }

    /**
     * hasSetting returns true when key exists, false otherwise.
     */
    public function test_hasSetting_returns_bool(): void
    {
        dispatchSetting('app_config', [
            'app_config_k' => 'v',
        ]);
        $this->assertTrue(hasSetting('app_config_k'));
        $this->assertFalse(hasSetting('nonexistent'));
    }

    /**
     * getSettingFromClass with fieldKey returns single value; with null returns full form array.
     */
    public function test_getSettingFromClass_returns_field_or_full_form(): void
    {
        dispatchSetting('app_config', [
            'app_config_site_name' => 'MyApp',
        ]);
        $this->assertSame('MyApp', getSettingFromClass(ConfigSettingStub::class, 'site_name'));
        $form = getSettingFromClass(ConfigSettingStub::class, null);
        $this->assertIsArray($form);
        $this->assertArrayHasKey('app_config_site_name', $form);
    }

    /**
     * invalidateSettingCache removes the setting cache key from cache.
     */
    public function test_invalidateSettingCache_clears_cache(): void
    {
        dispatchSetting('app_config', [
            'app_config_x' => 'y',
        ]);
        invalidateSettingCache();
        $this->assertFalse(Cache::has(config('setting.cache_key')));
    }

    /**
     * existsSetting returns true when form has at least one record, false otherwise.
     */
    public function test_existsSetting_returns_whether_form_has_records(): void
    {
        $this->assertFalse(existsSetting('no_form'));
        dispatchSetting('app_config', [
            'app_config_a' => '1',
        ]);
        $this->assertTrue(existsSetting('app_config'));
    }

    /**
     * getSettingsMany returns an array of values for given keys; missing keys get default.
     */
    public function test_getSettingsMany_returns_multiple_keys(): void
    {
        dispatchSetting('app_config', [
            'app_config_a' => '1',
            'app_config_b' => '2',
        ]);
        $result = getSettingsMany([
            'app_config_a',
            'app_config_b',
            'missing',
        ], 'default');
        $this->assertSame('1', $result['app_config_a']);
        $this->assertSame('2', $result['app_config_b']);
        $this->assertSame('default', $result['missing']);
    }

    /**
     * formSettingFromClass returns form data for the given AbstractSetting class.
     */
    public function test_formSettingFromClass_returns_form_by_class(): void
    {
        dispatchSetting('app_config', [
            'app_config_title' => 'Test',
        ]);
        $form = formSettingFromClass(ConfigSettingStub::class);
        $this->assertArrayHasKey('app_config_title', $form);
        $this->assertSame('Test', $form['app_config_title']);
    }

    /**
     * dispatchSetting with has_event false still stores and value is readable.
     */
    public function test_dispatchSetting_with_has_event_false_does_not_throw(): void
    {
        dispatchSetting('app_config', [
            'app_config_a' => 'b',
        ], false);
        $this->assertSame('b', getSetting('app_config_a'));
    }

    /**
     * getSettingFromClass throws SettingClassInvalidException when class does not extend AbstractSetting.
     */
    public function test_getSettingFromClass_throws_for_invalid_class(): void
    {
        $this->expectException(SettingClassInvalidException::class);

        getSettingFromClass(stdClass::class, 'key');
    }

    /**
     * formSettingFromClass throws SettingClassInvalidException for invalid class.
     */
    public function test_formSettingFromClass_throws_for_invalid_class(): void
    {
        $this->expectException(SettingClassInvalidException::class);

        formSettingFromClass(stdClass::class);
    }
}

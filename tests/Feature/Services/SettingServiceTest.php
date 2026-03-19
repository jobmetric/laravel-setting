<?php

namespace JobMetric\Setting\Tests\Feature\Services;

use Illuminate\Support\Facades\Cache;
use JobMetric\Setting\Exceptions\SettingClassInvalidException;
use JobMetric\Setting\Facades\Setting;
use JobMetric\Setting\Tests\Stubs\ConfigSettingStub;
use JobMetric\Setting\Tests\TestCase;
use stdClass;

/**
 * Feature tests for Setting service (Services\Setting).
 *
 * Purpose: Ensure the core service correctly stores/retrieves settings, builds cache, and respects form prefix.
 * Tests: dispatchByForm (storage, cache, prefix filter, JSON values), forget, form, all, get, has, getMany,
 * exists, invalidateCache, formFromClass, getFromClass (field and full form), and SettingClassInvalidException.
 */
class SettingServiceTest extends TestCase
{
    /**
     * dispatchByForm persists keys with form prefix and they are readable via get; has returns true.
     */
    public function test_dispatchByForm_stores_and_builds_cache(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_site_name' => 'My Site',
            'app_config_site_url'  => 'https://example.com',
        ]);

        $this->assertSame('My Site', Setting::get('app_config_site_name'));
        $this->assertSame('https://example.com', Setting::get('app_config_site_url'));
        $this->assertTrue(Setting::has('app_config_site_name'));
    }

    /**
     * dispatchByForm ignores object keys that do not start with the form prefix.
     */
    public function test_dispatchByForm_ignores_keys_without_form_prefix(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_foo' => 'bar',
            'other_form_baz' => 'ignored',
        ]);

        $this->assertSame('bar', Setting::get('app_config_foo'));
        $this->assertFalse(Setting::has('other_form_baz'));
    }

    /**
     * dispatchByForm stores array values as JSON and get returns decoded array.
     */
    public function test_dispatchByForm_stores_json_for_array_values(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_options' => [
                'a' => 1,
                'b' => 2,
            ],
        ]);

        $this->assertSame([
            'a' => 1,
            'b' => 2,
        ], Setting::get('app_config_options'));
    }

    /**
     * forget deletes all rows for the form and invalidates cache; get returns null for that form key.
     */
    public function test_forget_removes_form_from_database_and_cache(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_key' => 'value',
        ]);
        $this->assertTrue(Setting::exists('app_config'));

        Setting::forget('app_config');

        $this->assertFalse(Setting::exists('app_config'));
        $this->assertNull(Setting::get('app_config_key'));
    }

    /**
     * form returns only keys that start with the given form name.
     */
    public function test_form_returns_only_keys_for_given_form(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_a' => '1',
            'app_config_b' => '2',
        ]);
        Setting::dispatchByForm('app_other', [
            'app_other_x' => '3',
        ]);

        $form = Setting::form('app_config');

        $this->assertArrayHasKey('app_config_a', $form);
        $this->assertArrayHasKey('app_config_b', $form);
        $this->assertArrayNotHasKey('app_other_x', $form);
        $this->assertSame('1', $form['app_config_a']);
    }

    /**
     * all returns the full cached key-value map after at least one dispatch/build.
     */
    public function test_all_returns_full_cache_after_build(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_x' => 'y',
        ]);

        $all = Setting::all();

        $this->assertIsArray($all);
        $this->assertArrayHasKey('app_config_x', $all);
        $this->assertSame('y', $all['app_config_x']);
    }

    /**
     * get returns null or the given default when key is missing.
     */
    public function test_get_returns_default_for_missing_key(): void
    {
        $this->assertNull(Setting::get('missing_key'));
        $this->assertSame('default', Setting::get('missing_key', 'default'));
    }

    /**
     * getMany returns an array of values for the given keys; missing keys get default.
     */
    public function test_getMany_returns_values_for_keys(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_a' => '1',
            'app_config_b' => '2',
        ]);

        $result = Setting::getMany(['app_config_a', 'app_config_b', 'missing'], 'default');

        $this->assertSame('1', $result['app_config_a']);
        $this->assertSame('2', $result['app_config_b']);
        $this->assertSame('default', $result['missing']);
    }

    /**
     * exists returns false when no records exist for the form.
     */
    public function test_exists_returns_false_when_no_records(): void
    {
        $this->assertFalse(Setting::exists('nonexistent_form'));
    }

    /**
     * invalidateCache removes the setting cache key so next all() will rebuild from DB.
     */
    public function test_invalidateCache_clears_cache(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_k' => 'v',
        ]);
        $this->assertSame('v', Setting::get('app_config_k'));

        Setting::invalidateCache();

        $this->assertFalse(Cache::has(config('setting.cache_key')));
    }

    /**
     * formFromClass returns form key-value map for a valid AbstractSetting class.
     */
    public function test_formFromClass_returns_form_data_for_valid_class(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_title' => 'Test Title',
        ]);

        $form = Setting::formFromClass(ConfigSettingStub::class);

        $this->assertIsArray($form);
        $this->assertArrayHasKey('app_config_title', $form);
        $this->assertSame('Test Title', $form['app_config_title']);
    }

    /**
     * formFromClass throws SettingClassInvalidException when class does not extend AbstractSetting.
     */
    public function test_formFromClass_throws_for_invalid_class(): void
    {
        $this->expectException(SettingClassInvalidException::class);

        Setting::formFromClass(stdClass::class);
    }

    /**
     * getFromClass with fieldKey returns the value for formName_fieldKey.
     */
    public function test_getFromClass_with_fieldKey_returns_single_value(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_site_name' => 'My Site',
        ]);

        $value = Setting::getFromClass(ConfigSettingStub::class, 'site_name');

        $this->assertSame('My Site', $value);
    }

    /**
     * getFromClass with null fieldKey returns the full form array.
     */
    public function test_getFromClass_with_null_fieldKey_returns_full_form(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_a' => '1',
            'app_config_b' => '2',
        ]);

        $form = Setting::getFromClass(ConfigSettingStub::class, null);

        $this->assertIsArray($form);
        $this->assertArrayHasKey('app_config_a', $form);
        $this->assertArrayHasKey('app_config_b', $form);
        $this->assertSame('1', $form['app_config_a']);
        $this->assertSame('2', $form['app_config_b']);
    }

    /**
     * getFromClass throws SettingClassInvalidException for non-AbstractSetting class.
     */
    public function test_getFromClass_throws_for_invalid_class(): void
    {
        $this->expectException(SettingClassInvalidException::class);

        Setting::getFromClass('InvalidClass', 'key');
    }

    /**
     * dispatchByForm with empty object does not insert any rows; exists stays false.
     */
    public function test_dispatchByForm_with_empty_object_does_not_insert(): void
    {
        Setting::dispatchByForm('app_config', []);
        $this->assertFalse(Setting::exists('app_config'));
    }

    /**
     * dispatchByForm with only keys not matching form prefix does not insert.
     */
    public function test_dispatchByForm_with_only_non_matching_keys_does_not_insert(): void
    {
        Setting::dispatchByForm('app_config', [
            'other_form_key' => 'v',
        ]);
        $this->assertFalse(Setting::exists('app_config'));
    }
}

<?php

namespace JobMetric\Setting\Tests\Unit\Models;

use JobMetric\Setting\Models\Setting;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for Setting model (Models\Setting).
 *
 * Purpose: Ensure the Eloquent model uses correct table name, scope and fillable/casts.
 * Tests: getTable() returns config value; scopeOfForm filters by form; fillable allows mass assign;
 * is_json casts to boolean.
 */
class SettingModelTest extends TestCase
{
    /**
     * getTable returns the table name from config (setting.tables.setting).
     */
    public function test_getTable_returns_config_table_name(): void
    {
        $model = new Setting;

        $this->assertSame('settings', $model->getTable());
    }

    /**
     * scopeOfForm restricts query to rows where form column equals the given value.
     */
    public function test_scopeOfForm_filters_by_form(): void
    {
        Setting::query()->create([
            'form'    => 'app_config',
            'key'     => 'a',
            'value'   => '1',
            'is_json' => false,
        ]);
        Setting::query()->create([
            'form'    => 'app_other',
            'key'     => 'b',
            'value'   => '2',
            'is_json' => false,
        ]);

        $found = Setting::ofForm('app_config')->get();

        $this->assertCount(1, $found);
        $this->assertSame('app_config', $found->first()->form);
        $this->assertSame('a', $found->first()->key);
    }

    /**
     * form, key, value, is_json are fillable and can be set via create().
     */
    public function test_fillable_attributes_can_be_mass_assigned(): void
    {
        $setting = Setting::query()->create([
            'form'    => 'test_form',
            'key'     => 'test_key',
            'value'   => 'test_value',
            'is_json' => false,
        ]);

        $this->assertSame('test_form', $setting->form);
        $this->assertSame('test_key', $setting->key);
        $this->assertSame('test_value', $setting->value);
        $this->assertFalse($setting->is_json);
    }

    /**
     * is_json attribute is cast to boolean.
     */
    public function test_is_json_casts_to_boolean(): void
    {
        $setting = Setting::query()->create([
            'form'    => 'f',
            'key'     => 'k',
            'value'   => '{}',
            'is_json' => true,
        ]);

        $this->assertTrue($setting->is_json);
    }
}

<?php

namespace JobMetric\Setting\Tests\Feature;

use JobMetric\Setting\Facades\Setting;
use JobMetric\Setting\Facades\SettingNamespaceRegistry;
use JobMetric\Setting\Facades\SettingRegistry;
use JobMetric\Setting\SettingServiceProvider;
use JobMetric\Setting\Tests\TestCase;

/**
 * Feature tests for SettingServiceProvider and what it registers.
 *
 * Purpose: Ensure the provider boots correctly and facades/config are available.
 * Tests: Setting, SettingNamespaceRegistry and SettingRegistry facades resolve as singletons;
 * config values for tables, cache_key, cache_time; provider registration in the application.
 */
class SettingServiceProviderTest extends TestCase
{
    /**
     * Setting facade resolves to the Setting service instance.
     */
    public function test_setting_facade_resolves_service(): void
    {
        $service = Setting::getFacadeRoot();

        $this->assertNotNull($service);
    }

    /**
     * SettingNamespaceRegistry facade resolves as singleton (same instance on multiple calls).
     */
    public function test_setting_namespace_registry_facade_resolves_singleton(): void
    {
        $registry = SettingNamespaceRegistry::getFacadeRoot();

        $this->assertNotNull($registry);
        $this->assertSame(SettingNamespaceRegistry::getFacadeRoot(), $registry);
    }

    /**
     * SettingRegistry facade resolves as singleton.
     */
    public function test_setting_registry_facade_resolves_singleton(): void
    {
        $registry = SettingRegistry::getFacadeRoot();

        $this->assertNotNull($registry);
        $this->assertSame(SettingRegistry::getFacadeRoot(), $registry);
    }

    /**
     * Config setting.tables.setting is loaded (default: settings).
     */
    public function test_config_setting_tables_are_loaded(): void
    {
        $this->assertSame('settings', config('setting.tables.setting'));
    }

    /**
     * Config cache_key and cache_time are loaded for tests.
     */
    public function test_config_cache_key_and_time_are_loaded(): void
    {
        $this->assertSame('SETTING', config('setting.cache_key'));
        $this->assertSame(60, config('setting.cache_time'));
    }

    /**
     * SettingServiceProvider is in the application's loaded providers list.
     */
    public function test_provider_is_registered(): void
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(SettingServiceProvider::class, $providers);
        $this->assertTrue($providers[SettingServiceProvider::class]);
    }
}

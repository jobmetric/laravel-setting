<?php

namespace JobMetric\Setting\Tests\Unit\Support;

use JobMetric\Setting\Support\SettingNamespaceRegistry;
use JobMetric\Setting\Support\SettingRegistry;
use JobMetric\Setting\Tests\Stubs\ConfigSettingStub;
use JobMetric\Setting\Tests\TestCase;
use stdClass;

/**
 * Unit tests for SettingRegistry (Support\SettingRegistry).
 *
 * Purpose: Ensure the registry of AbstractSetting class FQCNs only allows classes from registered
 * namespaces that extend AbstractSetting; resolveSpec returns metadata; unregister/clear work.
 * Tests: register adds when class in namespace and extends AbstractSetting; register ignores when
 * namespace not in SettingNamespaceRegistry or class not AbstractSetting; has, unregister, resolveSpec,
 * clear; register/unregister return self.
 */
class SettingRegistryTest extends TestCase
{
    protected SettingNamespaceRegistry $namespaceRegistry;

    protected SettingRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->namespaceRegistry = new SettingNamespaceRegistry();
        $this->namespaceRegistry->register('JobMetric\\Setting\\Tests\\Stubs');
        $this->registry = new SettingRegistry($this->namespaceRegistry);
    }

    /**
     * register() adds a class that extends AbstractSetting and lives in a registered namespace.
     */
    public function test_register_adds_allowed_class(): void
    {
        $this->registry->register(ConfigSettingStub::class);

        $this->assertTrue($this->registry->has(ConfigSettingStub::class));
        $this->assertContains(ConfigSettingStub::class, $this->registry->all());
    }

    /**
     * register() does not add a class whose namespace is not in SettingNamespaceRegistry.
     */
    public function test_register_ignores_class_outside_namespace(): void
    {
        $this->namespaceRegistry->clear();
        $this->namespaceRegistry->register('Other\\Namespace');
        $registry = new SettingRegistry($this->namespaceRegistry);

        $registry->register(ConfigSettingStub::class);

        $this->assertFalse($registry->has(ConfigSettingStub::class));
        $this->assertSame([], $registry->all());
    }

    /**
     * unregister() removes the class from the registry.
     */
    public function test_unregister_removes_class(): void
    {
        $this->registry->register(ConfigSettingStub::class);
        $this->registry->unregister(ConfigSettingStub::class);

        $this->assertFalse($this->registry->has(ConfigSettingStub::class));
    }

    /**
     * resolveSpec() returns array with application, key, form_name etc. for a registered setting class.
     */
    public function test_resolveSpec_returns_array_for_registered_setting(): void
    {
        $this->registry->register(ConfigSettingStub::class);

        $spec = $this->registry->resolveSpec(ConfigSettingStub::class);

        $this->assertIsArray($spec);
        $this->assertArrayHasKey('application', $spec);
        $this->assertArrayHasKey('key', $spec);
        $this->assertArrayHasKey('form_name', $spec);
        $this->assertSame('app', $spec['application']);
        $this->assertSame('config', $spec['key']);
        $this->assertSame('app_config', $spec['form_name']);
    }

    /**
     * resolveSpec() returns null for a non-existent class name.
     */
    public function test_resolveSpec_returns_null_for_non_existent_class(): void
    {
        $spec = $this->registry->resolveSpec('NonExistent\\Class');

        $this->assertNull($spec);
    }

    /**
     * clear() removes all registered setting classes.
     */
    public function test_clear_removes_all_settings(): void
    {
        $this->registry->register(ConfigSettingStub::class);
        $this->registry->clear();

        $this->assertSame([], $this->registry->all());
    }

    /**
     * register() returns self for chaining.
     */
    public function test_register_returns_self(): void
    {
        $result = $this->registry->register(ConfigSettingStub::class);

        $this->assertSame($this->registry, $result);
    }

    /**
     * register() does not add stdClass or any class that does not extend AbstractSetting.
     */
    public function test_register_ignores_non_setting_class(): void
    {
        $this->registry->register(stdClass::class);

        $this->assertFalse($this->registry->has(stdClass::class));
        $this->assertSame([], $this->registry->all());
    }

    /**
     * has() returns false for a class that was never registered.
     */
    public function test_has_returns_false_for_unregistered_class(): void
    {
        $this->assertFalse($this->registry->has(ConfigSettingStub::class));
    }

    /**
     * unregister() returns self for chaining.
     */
    public function test_unregister_returns_self(): void
    {
        $this->registry->register(ConfigSettingStub::class);
        $result = $this->registry->unregister(ConfigSettingStub::class);

        $this->assertSame($this->registry, $result);
    }
}

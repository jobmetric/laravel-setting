<?php

namespace JobMetric\Setting\Tests\Unit;

use JobMetric\Setting\Facades\SettingType;
use JobMetric\Setting\SettingType as SettingTypeClass;
use JobMetric\Setting\Tests\TestCase;
use ReflectionException;
use ReflectionMethod;

/**
 * Unit tests for SettingType (type system used by StoreSettingRequest).
 *
 * Purpose: Ensure SettingType is registered and exposes the correct type name.
 * Tests: SettingType facade resolves from container; same instance on multiple calls (singleton);
 * typeName() returns 'setting-type' (via reflection, method is protected).
 */
class SettingTypeTest extends TestCase
{
    /**
     * SettingType facade resolves to a non-null instance.
     */
    public function test_setting_type_facade_resolves(): void
    {
        $this->assertNotNull(SettingType::getFacadeRoot());
    }

    /**
     * SettingType is registered as singleton (same instance on multiple getFacadeRoot calls).
     */
    public function test_setting_type_is_singleton(): void
    {
        $this->assertSame(SettingType::getFacadeRoot(), SettingType::getFacadeRoot());
    }

    /**
     * typeName() (protected) returns 'setting-type'.
     *
     * @throws ReflectionException
     */
    public function test_type_name_returns_setting_type(): void
    {
        $type = $this->app->make(SettingTypeClass::class);
        $method = new ReflectionMethod($type, 'typeName');

        $this->assertSame('setting-type', $method->invoke($type));
    }
}

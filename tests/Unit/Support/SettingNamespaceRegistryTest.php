<?php

namespace JobMetric\Setting\Tests\Unit\Support;

use JobMetric\Setting\Support\SettingNamespaceRegistry;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for SettingNamespaceRegistry (Support\SettingNamespaceRegistry).
 *
 * Purpose: Ensure the registry that holds setting form namespaces (e.g. App\Settings) works correctly.
 * Tests: register adds and trims namespace; duplicate register does not add twice; unregister removes;
 * has returns true/false; all returns list; clear empties; register empty string does not add; register returns self.
 */
class SettingNamespaceRegistryTest extends TestCase
{
    protected SettingNamespaceRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new SettingNamespaceRegistry();
    }

    /**
     * register() adds the namespace and has() returns true; all() contains it.
     */
    public function test_register_adds_namespace(): void
    {
        $this->registry->register('App\\Settings');

        $this->assertTrue($this->registry->has('App\\Settings'));
        $this->assertContains('App\\Settings', $this->registry->all());
    }

    /**
     * register() trims leading/trailing backslashes from namespace.
     */
    public function test_register_trimmed_namespace(): void
    {
        $this->registry->register('\\\\Vendor\\Settings\\\\');

        $this->assertTrue($this->registry->has('Vendor\\Settings'));
    }

    /**
     * register() does not add the same namespace twice.
     */
    public function test_register_ignores_duplicate(): void
    {
        $this->registry->register('App\\Settings');
        $this->registry->register('App\\Settings');

        $this->assertCount(1, $this->registry->all());
    }

    /**
     * unregister() removes the namespace; has() returns false and all() is empty.
     */
    public function test_unregister_removes_namespace(): void
    {
        $this->registry->register('App\\Settings');
        $this->registry->unregister('App\\Settings');

        $this->assertFalse($this->registry->has('App\\Settings'));
        $this->assertSame([], $this->registry->all());
    }

    /**
     * has() returns false for a namespace that was never registered.
     */
    public function test_has_returns_false_for_unregistered(): void
    {
        $this->assertFalse($this->registry->has('Unknown\\Namespace'));
    }

    /**
     * clear() removes all namespaces; all() returns empty array.
     */
    public function test_clear_removes_all_namespaces(): void
    {
        $this->registry->register('App\\Settings');
        $this->registry->register('Vendor\\Settings');
        $this->registry->clear();

        $this->assertSame([], $this->registry->all());
        $this->assertFalse($this->registry->has('App\\Settings'));
    }

    /**
     * register() returns self for method chaining.
     */
    public function test_register_returns_self(): void
    {
        $result = $this->registry->register('App\\Settings');

        $this->assertSame($this->registry, $result);
    }

    /**
     * all() returns empty array when nothing was registered.
     */
    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->registry->all());
    }

    /**
     * register with empty or only-backslash string does not add any namespace.
     */
    public function test_register_empty_string_does_not_add(): void
    {
        $this->registry->register('');
        $this->registry->register('\\\\');

        $this->assertSame([], $this->registry->all());
    }
}

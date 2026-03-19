<?php

namespace JobMetric\Setting\Tests\Unit\Events;

use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for StoreSettingEvent (Events\StoreSettingEvent).
 *
 * Purpose: Ensure the event carries the form name and exposes key/definition for event system.
 * Tests: Constructor sets form property; key() returns 'setting.store'; definition() returns
 * DomainEventDefinition with key, tags (including 'setting').
 */
class StoreSettingEventTest extends TestCase
{
    /**
     * Constructor stores the form name in the event instance.
     */
    public function test_constructor_sets_form(): void
    {
        $event = new StoreSettingEvent('app_config');

        $this->assertSame('app_config', $event->form);
    }

    /**
     * key() returns the stable technical key used by the event system.
     */
    public function test_key_returns_stable_technical_key(): void
    {
        $this->assertSame('setting.store', StoreSettingEvent::key());
    }

    /**
     * definition() returns a DomainEventDefinition with key and tags.
     */
    public function test_definition_returns_domain_event_definition(): void
    {
        $def = StoreSettingEvent::definition();

        $this->assertNotNull($def);
        $this->assertSame('setting.store', $def->key);
        $this->assertIsArray($def->tags);
        $this->assertContains('setting', $def->tags);
    }
}

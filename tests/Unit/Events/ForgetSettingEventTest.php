<?php

namespace JobMetric\Setting\Tests\Unit\Events;

use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for ForgetSettingEvent (Events\ForgetSettingEvent).
 *
 * Purpose: Ensure the event carries the form name and exposes key/definition for event system.
 * Tests: Constructor sets form; key() returns 'setting.forget'; definition() returns
 * DomainEventDefinition with key and tags.
 */
class ForgetSettingEventTest extends TestCase
{
    /**
     * Constructor stores the form name in the event instance.
     */
    public function test_constructor_sets_form(): void
    {
        $event = new ForgetSettingEvent('app_config');

        $this->assertSame('app_config', $event->form);
    }

    /**
     * key() returns the stable technical key 'setting.forget'.
     */
    public function test_key_returns_stable_technical_key(): void
    {
        $this->assertSame('setting.forget', ForgetSettingEvent::key());
    }

    /**
     * definition() returns a DomainEventDefinition with key and tags.
     */
    public function test_definition_returns_domain_event_definition(): void
    {
        $def = ForgetSettingEvent::definition();

        $this->assertNotNull($def);
        $this->assertSame('setting.forget', $def->key);
        $this->assertIsArray($def->tags);
        $this->assertContains('setting', $def->tags);
    }
}

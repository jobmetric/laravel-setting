<?php

namespace JobMetric\Setting\Tests\Feature\Services;

use Illuminate\Support\Facades\Event;
use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Facades\Setting;
use JobMetric\Setting\Tests\TestCase;

/**
 * Feature tests for Setting service event dispatching.
 *
 * Purpose: Ensure StoreSettingEvent and ForgetSettingEvent are fired only when has_event is true.
 * Tests: dispatchByForm(..., true) dispatches StoreSettingEvent; dispatchByForm(..., false) does not;
 * forget(..., true) dispatches ForgetSettingEvent; forget(..., false) does not.
 */
class SettingServiceEventsTest extends TestCase
{
    /**
     * dispatchByForm with has_event true dispatches StoreSettingEvent with correct form.
     */
    public function test_dispatchByForm_with_has_event_fires_store_setting_event(): void
    {
        Event::fake([StoreSettingEvent::class]);

        Setting::dispatchByForm('app_config', [
            'app_config_foo' => 'bar',
        ], true);

        Event::assertDispatched(StoreSettingEvent::class, function (StoreSettingEvent $event) {
            return $event->form === 'app_config';
        });
    }

    /**
     * dispatchByForm with has_event false does not dispatch StoreSettingEvent.
     */
    public function test_dispatchByForm_without_event_does_not_fire_store_setting_event(): void
    {
        Event::fake([StoreSettingEvent::class]);

        Setting::dispatchByForm('app_config', [
            'app_config_foo' => 'bar',
        ], false);

        Event::assertNotDispatched(StoreSettingEvent::class);
    }

    /**
     * forget with has_event true dispatches ForgetSettingEvent with correct form.
     */
    public function test_forget_with_has_event_fires_forget_setting_event(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_x' => 'y',
        ], false);
        Event::fake([ForgetSettingEvent::class]);

        Setting::forget('app_config', true);

        Event::assertDispatched(ForgetSettingEvent::class, function (ForgetSettingEvent $event) {
            return $event->form === 'app_config';
        });
    }

    /**
     * forget with has_event false does not dispatch ForgetSettingEvent.
     */
    public function test_forget_without_event_does_not_fire_forget_setting_event(): void
    {
        Setting::dispatchByForm('app_config', [
            'app_config_x' => 'y',
        ], false);
        Event::fake([ForgetSettingEvent::class]);

        Setting::forget('app_config', false);

        Event::assertNotDispatched(ForgetSettingEvent::class);
    }
}

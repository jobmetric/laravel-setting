<?php

namespace JobMetric\Setting\Tests\Unit\Contracts;

use JobMetric\Setting\Tests\Stubs\ConfigSettingStub;
use JobMetric\Setting\Tests\TestCase;
use Throwable;

/**
 * Unit tests for AbstractSetting contract (Contracts\AbstractSetting).
 *
 * Purpose: Ensure the base contract for setting forms behaves correctly (formName, toArray).
 * Tests: formName() returns application_key; toArray() returns application, key, title, description,
 * form_name and form (form definition array). Uses ConfigSettingStub as concrete implementation.
 */
class AbstractSettingTest extends TestCase
{
    /**
     * formName returns application and key joined by underscore (e.g. app_config).
     */
    public function test_formName_returns_application_underscore_key(): void
    {
        $stub = new ConfigSettingStub;

        $this->assertSame('app_config', $stub->formName());
    }

    /**
     * toArray contains application, key, title, description, form_name and form (array) keys.
     *
     * @throws Throwable
     */
    public function test_toArray_contains_application_key_title_description_form_name_and_form(): void
    {
        $stub = new ConfigSettingStub;
        $arr = $stub->toArray();

        $this->assertSame('app', $arr['application']);
        $this->assertSame('config', $arr['key']);
        $this->assertSame('Config Stub', $arr['title']);
        $this->assertSame('Stub for testing', $arr['description']);
        $this->assertSame('app_config', $arr['form_name']);
        $this->assertIsArray($arr['form']);
    }
}

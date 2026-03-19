<?php

namespace JobMetric\Setting\Tests\Stubs;

use JobMetric\Form\FormBuilder;
use JobMetric\Setting\Contracts\AbstractSetting;

/**
 * Stub AbstractSetting for tests. Form name: app_config.
 */
class ConfigSettingStub extends AbstractSetting
{
    public function application(): string
    {
        return 'app';
    }

    public function key(): string
    {
        return 'config';
    }

    public function title(): string
    {
        return 'Config Stub';
    }

    public function description(): string
    {
        return 'Stub for testing';
    }

    public function form(): FormBuilder
    {
        return new FormBuilder();
    }
}

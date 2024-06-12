<?php

namespace JobMetric\Setting\Tests;

use Tests\BaseDatabaseTestCase as BaseTestCase;
use JobMetric\Setting\Facades\Setting as SettingFacade;

class SettingTest extends BaseTestCase
{
    public function testStore(): void
    {
        // store setting
        SettingFacade::dispatch('config', [
            'config_test1' => 'test1.1',
            'config_test2' => 'test2.1',
        ]);

        $this->assertDatabaseHas('settings', [
            'code' => 'config',
            'key' => 'test1',
            'value' => 'test1.1',
            'is_json' => 0,
        ]);

        $this->assertDatabaseHas('settings', [
            'code' => 'config',
            'key' => 'test2',
            'value' => 'test2.1',
            'is_json' => 0,
        ]);
    }

    public function testForget(): void
    {
        // store setting
        SettingFacade::dispatch('config', [
            'config_test1' => 'test1.1',
            'config_test2' => 'test2.1',
        ]);

        // forget setting
        SettingFacade::forget('config');

        $this->assertDatabaseMissing('settings', [
            'code' => 'config',
        ]);
    }

    public function testSetAll(): void
    {
        // set all settings
        SettingFacade::setAll([
            'config_test1' => 'test1.2',
            'config_test2' => 'test2.2',
        ]);

        $this->assertEquals('test1.2', SettingFacade::get('config_test1'));
        $this->assertEquals('test2.2', SettingFacade::get('config_test2'));
    }

    public function testSet(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.3');

        $this->assertEquals('test1.3', SettingFacade::get('config_test1'));
    }

    public function testGet(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.4');

        $this->assertEquals('test1.4', SettingFacade::get('config_test1'));
    }

    public function testCode(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.5');

        $this->assertEquals([
            'config_test1' => 'test1.5',
        ], SettingFacade::code('config'));
    }

    public function testHas(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.6');

        $this->assertTrue(SettingFacade::has('config_test1'));
    }

    public function testUnset(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.7');

        // unset setting
        SettingFacade::unset('config_test1');

        $this->assertNull(SettingFacade::get('config_test1'));
    }

    public function testAll(): void
    {
        // set setting
        SettingFacade::set('config_test1', 'test1.8');
        SettingFacade::set('config_test2', 'test2.8');

        $this->assertEquals([
            'config_test1' => 'test1.8',
            'config_test2' => 'test2.8',
        ], SettingFacade::all());
    }
}

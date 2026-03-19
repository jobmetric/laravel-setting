<?php

namespace JobMetric\Setting\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JobMetric\Setting\SettingServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base test case for Setting package tests.
 *
 * Used by all tests in the package. Uses RefreshDatabase; loads package migrations when
 * settings table is missing. Sets setting config (tables, cache_key, cache_time) in getEnvironmentSetUp.
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SettingServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('setting.tables', [
            'setting' => 'settings',
        ]);
        $app['config']->set('setting.cache_key', 'SETTING');
        $app['config']->set('setting.cache_time', 60);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->app['db']->getSchemaBuilder()->hasTable(config('setting.tables.setting'))) {
            loadMigrationPath(__DIR__ . '/../database/migrations');
        }
    }
}

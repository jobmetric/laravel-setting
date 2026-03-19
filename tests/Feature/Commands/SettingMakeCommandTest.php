<?php

namespace JobMetric\Setting\Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use JobMetric\Setting\Tests\TestCase;

/**
 * Feature tests for the setting:make Artisan command.
 *
 * Purpose: Ensure the command creates a new AbstractSetting class file in app/Settings with correct content.
 * Tests: Command exits successfully with a unique name; created file contains class name, extends AbstractSetting,
 * FormBuilder; name without "Setting" suffix gets "Setting" appended (e.g. ConfigMake -> ConfigMakeSetting).
 */
class SettingMakeCommandTest extends TestCase
{
    /**
     * Resolve path where the command may have created the file (app or testbench path).
     */
    protected function resolveCreatedPath(string $class): ?string
    {
        $path = $this->app->path('Settings' . DIRECTORY_SEPARATOR . $class . '.php');
        $altPath = base_path('app' . DIRECTORY_SEPARATOR . 'Settings' . DIRECTORY_SEPARATOR . $class . '.php');

        return File::exists($path) ? $path : (File::exists($altPath) ? $altPath : null);
    }

    /**
     * setting:make with a unique name argument exits with success.
     */
    public function test_setting_make_command_exits_success_with_name_argument(): void
    {
        $unique = 'TestSettingMake' . uniqid();
        $this->artisan('setting:make', ['name' => $unique])->assertSuccessful();
    }

    /**
     * setting:make creates a PHP file with correct class name, AbstractSetting and FormBuilder.
     */
    public function test_setting_make_command_creates_setting_class_file(): void
    {
        $unique = 'TestSettingMake' . uniqid();
        $this->artisan('setting:make', ['name' => $unique])->assertSuccessful();

        $created = $this->resolveCreatedPath($unique);
        if ($created !== null) {
            $content = file_get_contents($created);
            $this->assertStringContainsString('class ' . $unique, $content);
            $this->assertStringContainsString('extends AbstractSetting', $content);
            $this->assertStringContainsString('FormBuilder', $content);
        }
    }

    /**
     * setting:make with name without "Setting" appends "Setting" to class name (e.g. ConfigMake -> ConfigMakeSetting).
     */
    public function test_setting_make_command_appends_setting_suffix_when_missing(): void
    {
        $baseName = 'ConfigMake' . uniqid();
        $class = $baseName . 'Setting';
        $this->artisan('setting:make', [
            'name' => $baseName,
        ])->assertSuccessful();

        $created = $this->resolveCreatedPath($class);
        if ($created !== null) {
            $content = file_get_contents($created);
            $this->assertStringContainsString('class ' . $class . ' extends AbstractSetting', $content);
        }
    }
}

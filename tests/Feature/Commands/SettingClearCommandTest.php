<?php

namespace JobMetric\Setting\Tests\Feature\Commands;

use JobMetric\Setting\Tests\TestCase;

/**
 * Feature tests for the setting:clear Artisan command.
 *
 * Purpose: Ensure the command is runnable and exits successfully (clears setting cache).
 * Tests: Running php artisan setting:clear returns exit code 0.
 */
class SettingClearCommandTest extends TestCase
{
    /**
     * setting:clear command runs without error and exits with success code.
     */
    public function test_setting_clear_command_exits_success(): void
    {
        $this->artisan('setting:clear')->assertSuccessful();
    }
}

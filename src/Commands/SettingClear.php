<?php

namespace JobMetric\Setting\Commands;

use Illuminate\Console\Command;
use JobMetric\PackageCore\Commands\ConsoleTools;
use JobMetric\Setting\Facades\Setting;

/**
 * Clear the setting cache.
 *
 * @package JobMetric\Setting
 */
class SettingClear extends Command
{
    use ConsoleTools;

    /**
     * Console command signature.
     *
     * @var string
     */
    protected $signature = 'setting:clear';

    /**
     * Console command description.
     *
     * @var string
     */
    protected $description = 'Clear the setting cache';

    /**
     * Run the command.
     *
     * @return int
     */
    public function handle(): int
    {
        Setting::invalidateCache();
        $this->message('Setting cache cleared successfully.', 'success');

        return 0;
    }
}

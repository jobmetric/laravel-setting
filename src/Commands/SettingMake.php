<?php

namespace JobMetric\Setting\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JobMetric\PackageCore\Commands\ConsoleTools;

/**
 * Create a new setting form class in app/Settings.
 *
 * @package JobMetric\Setting
 */
class SettingMake extends Command
{
    use ConsoleTools;

    /**
     * Console command signature.
     *
     * @var string
     */
    protected $signature = 'setting:make
        {name? : Setting class name (e.g. ConfigSetting or Config)}
        {--a|application= : Application name (default: app)}
        {--t|title= : Human-readable title (default: from class name)}
        {--d|description= : Short description}';

    /**
     * Console command description.
     *
     * @var string
     */
    protected $description = 'Create a new setting form class in app/Settings';

    /**
     * Run the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $name = $name !== null && trim((string) $name) !== '' ? trim($name) : null;

        if ($name === null) {
            $name = $this->ask('Setting class name (e.g. ConfigSetting or Config)');
            if ($name === null || trim($name) === '') {
                $this->message('Name is required.', 'error');

                return 1;
            }

            $name = trim($name);
        }

        $class = Str::studly($name);
        if (! str_ends_with($class, 'Setting')) {
            $class .= 'Setting';
        }

        $namespace = trim(appNamespace(), '\\') . '\\Settings';
        $path = app_path('Settings' . DIRECTORY_SEPARATOR . $class . '.php');

        if ($this->isFile($path)) {
            $this->message('Setting already exists: ' . $class, 'error');

            return 2;
        }

        $key = Str::snake(Str::replaceLast('Setting', '', $class));

        $application = $this->option('application');
        $application = $application !== null && trim((string) $application) !== '' ? trim($application) : 'app';

        $title = $this->option('title');
        $title = $title !== null && trim((string) $title) !== '' ? trim($title) : Str::headline($key);

        $description = $this->option('description');
        $description = $description !== null ? trim((string) $description) : '';

        $items = [
            'namespace'   => $namespace,
            'class'       => $class,
            'application' => $application,
            'key'         => $key,
            'title'       => $title,
            'description' => $description,
        ];

        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stub' . DIRECTORY_SEPARATOR . 'setting';
        $content = $this->getStub($stubPath, $items);

        $dir = dirname($path);
        if (! $this->isDir($dir)) {
            $this->makeDir($dir);
        }

        $this->putFile($path, $content);
        $this->message('Setting [' . $class . '] created successfully at app/Settings/' . $class . '.php', 'success');

        return 0;
    }
}

<?php

namespace JobMetric\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;
use JobMetric\Setting\Models\Setting as SettingModel;

class SettingServiceProvider extends PackageCoreServiceProvider
{
    /**
     * @param PackageCore $package
     *
     * @return void
     * @throws MigrationFolderNotFoundException
     * @throws RegisterClassTypeNotFoundException
     */
    public function configuration(PackageCore $package): void
    {
        $package->name('laravel-setting')
            ->hasConfig()
            ->hasMigration()
            ->registerClass('Setting', Setting::class)
            ->registerClass('SettingType', SettingType::class);
    }

    /**
     * after boot package
     *
     * @return void
     */
    public function afterBootPackage(): void
    {
        $settings = Cache::remember('setting', config('setting.cache_time'), function () {
            $data = [];
            if (Schema::hasTable(config('setting.tables.setting'))) {
                $results = SettingModel::all();

                foreach ($results as $setting) {
                    $data[$setting->form . '_' . $setting->key] = ($setting->is_json) ? json_decode($setting->value, true) : $setting->value;
                }
            }

            return $data;
        });

        app(Setting::class)->setAll($settings);
    }
}

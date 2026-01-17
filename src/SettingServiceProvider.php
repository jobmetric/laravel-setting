<?php

namespace JobMetric\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;
use JobMetric\Setting\Facades\Setting as SettingFacade;
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
            ->hasTranslation()
            ->registerClass('Setting', Setting::class)
            ->registerClass('SettingType', SettingType::class, RegisterClassTypeEnum::SINGLETON());
    }

    /**
     * After register package
     *
     * @return void
     */
    public function afterRegisterPackage(): void
    {
        // Register model binding
        Route::model('jm_setting', SettingModel::class);
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

        SettingFacade::setAll($settings);
    }
}

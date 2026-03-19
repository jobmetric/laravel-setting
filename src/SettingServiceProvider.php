<?php

namespace JobMetric\Setting;

use Illuminate\Support\Facades\App;
use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;
use JobMetric\Setting\Facades\SettingNamespaceRegistry as FacadeSettingNamespaceRegistry;
use JobMetric\Setting\Facades\SettingRegistry as FacadeSettingRegistry;
use JobMetric\Setting\Services\Setting as SettingService;
use JobMetric\Setting\Support\SettingNamespaceRegistry;
use JobMetric\Setting\Support\SettingRegistry;

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
            ->registerCommand(Commands\SettingMake::class)
            ->registerCommand(Commands\SettingClear::class)
            ->registerClass('Setting', SettingService::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('SettingNamespaceRegistry', SettingNamespaceRegistry::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('SettingRegistry', SettingRegistry::class, RegisterClassTypeEnum::SINGLETON());
    }

    /**
     * After register package
     *
     * @return void
     */
    public function afterRegisterPackage(): void
    {
        // register setting default namespace
        FacadeSettingNamespaceRegistry::register(appNamespace() . "Settings");

        App::booting(function () {
            FacadeSettingRegistry::discover();
        });
    }
}

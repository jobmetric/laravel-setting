<?php

namespace JobMetric\Setting;

use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;
use JobMetric\Setting\Facades\SettingNamespaceRegistry as FacadeSettingNamespaceRegistry;
use JobMetric\Setting\Support\SettingNamespaceRegistry;

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
            ->registerClass('Setting', Setting::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('SettingType', SettingType::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('SettingNamespaceRegistry', SettingNamespaceRegistry::class, RegisterClassTypeEnum::SINGLETON());
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
    }
}

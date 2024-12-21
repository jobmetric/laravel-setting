<?php

namespace JobMetric\Setting;

use Illuminate\Support\Traits\Macroable;
use JobMetric\Form\FormServiceType;
use JobMetric\PackageCore\Services\BaseServiceType;
use JobMetric\PackageCore\Services\InformationServiceType;
use JobMetric\PackageCore\Services\ListShowDescriptionServiceType;
use JobMetric\PackageCore\Services\ServiceType;

class SettingType extends ServiceType
{
    use Macroable,
        BaseServiceType,
        FormServiceType,
        InformationServiceType,
        ListShowDescriptionServiceType;

    protected function serviceType(): string
    {
        return 'settingType';
    }
}

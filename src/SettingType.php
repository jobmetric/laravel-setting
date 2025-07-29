<?php

namespace JobMetric\Setting;

use Illuminate\Support\Traits\Macroable;
use JobMetric\Form\Typeify\HasFormType;
use JobMetric\Typeify\BaseType;
use JobMetric\Typeify\Traits\List\ShowDescriptionInListType;

class SettingType extends BaseType
{
    use Macroable,
        HasFormType,
        ShowDescriptionInListType;

    protected function typeName(): string
    {
        return 'setting-type';
    }
}

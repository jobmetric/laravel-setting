<?php

namespace JobMetric\Setting\Exceptions;

use Exception;
use JobMetric\Setting\Contracts\AbstractSetting;
use Throwable;

/**
 * Thrown when a given class is not a subclass of AbstractSetting.
 *
 * @package JobMetric\Setting
 */
class SettingClassInvalidException extends Exception
{
    public function __construct(string $class, ?string $message = null, int $code = 400, ?Throwable $previous = null)
    {
        $message = $message ?? trans('setting::base.exceptions.setting_class_invalid', [
            'expected' => AbstractSetting::class,
            'given'    => $class,
        ]);

        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace JobMetric\Setting\Events;

class ForgetSettingEvent
{
    public string $code;

    /**
     * Create a new event instance.
     *
     * @param string $code
     *
     * @return void
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }
}

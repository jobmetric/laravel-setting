<?php

namespace JobMetric\Setting\Events;

class StoreSettingEvent
{
    public string $form;

    /**
     * Create a new event instance.
     *
     * @param string $form
     *
     * @return void
     */
    public function __construct(string $form)
    {
        $this->form = $form;
    }
}

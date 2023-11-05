<?php

namespace JobMetric\Setting;

use Illuminate\Contracts\Foundation\Application;

class Setting
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new EnvModifier instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}

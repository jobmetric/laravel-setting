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
     * The setting data.
     *
     * @var array
     */
    private array $data = [];

    /**
     * Create a new Setting instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * set all setting
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setAll(mixed $value): void
    {
        $this->data = $value;
    }

    /**
     * set setting
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * get setting
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return (isset($this->data[$key])) ? $this->data[$key] : $default;
    }

    /**
     * get code setting
     *
     * @param string $code
     *
     * @return array
     */
    public function code(string $code): array
    {
        $setting = [];
        foreach ($this->data as $key => $value) {
            if (str_starts_with($key, $code)) {
                $setting[$key] = $this->data[$key];
            }
        }

        return $setting;
    }

    /**
     * has setting
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * unset setting
     *
     * @param string $key
     *
     * @return void
     */
    public function unset(string $key): void
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }
    }

    /**
     * get all setting
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }
}

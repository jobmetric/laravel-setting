<?php

namespace JobMetric\Setting;

use Illuminate\Contracts\Foundation\Application;
use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Facades\Setting as SettingFacade;
use JobMetric\Setting\Models\Setting as SettingModel;

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
     * dispatch setting
     *
     * @param string $code
     * @param array $object
     * @param bool $has_event
     *
     * @return void
     */
    public function dispatch(string $code, array $object, bool $has_event = true): void
    {
        $this->forget($code, $has_event);

        foreach ($object as $index => $item) {
            if (str_starts_with($index, $code)) {
                $key = substr($index, (strlen($code) + 1), (strlen($index) - (strlen($code) + 1)));

                SettingModel::create([
                    'code' => $code,
                    'key' => $key,
                    'value' => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item,
                    'is_json' => is_array($item),
                ]);

                SettingFacade::set($code . '_' . $key, $item);
            }
        }

        if ($has_event) {
            event(new StoreSettingEvent($code));
        }

        cache()->forget('setting');
    }

    /**
     * forget setting
     *
     * @param string $code
     * @param bool $has_event
     *
     * @return void
     */
    public function forget(string $code, bool $has_event = true): void
    {
        SettingModel::ofCode($code)->get()->each(function ($item) {
            SettingFacade::unset($item->code . '_' . $item->key);

            $item->delete();
        });

        if ($has_event) {
            event(new ForgetSettingEvent($code));
        }

        cache()->forget('setting');
    }

    /**
     * set all settings
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
     * get all settings
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }
}

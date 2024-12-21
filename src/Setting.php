<?php

namespace JobMetric\Setting;

use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Facades\Setting as SettingFacade;
use JobMetric\Setting\Models\Setting as SettingModel;

class Setting
{
    /**
     * The setting data.
     *
     * @var array
     */
    private array $data = [];

    /**
     * dispatch setting
     *
     * @param string $form
     * @param array $object
     * @param bool $has_event
     *
     * @return void
     */
    public function dispatch(string $form, array $object, bool $has_event = true): void
    {
        $this->forget($form, $has_event);

        foreach ($object as $index => $item) {
            if (str_starts_with($index, $form)) {
                $key = substr($index, (strlen($form) + 1), (strlen($index) - (strlen($form) + 1)));

                SettingModel::create([
                    'form' => $form,
                    'key' => $key,
                    'value' => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item,
                    'is_json' => is_array($item),
                ]);

                SettingFacade::set($form . '_' . $key, $item);
            }
        }

        if ($has_event) {
            event(new StoreSettingEvent($form));
        }
    }

    /**
     * forget setting
     *
     * @param string $form
     * @param bool $has_event
     *
     * @return void
     */
    public function forget(string $form, bool $has_event = true): void
    {
        SettingModel::ofForm($form)->get()->each(function ($item) {
            SettingFacade::unset($item->form . '_' . $item->key);

            $item->delete();
        });

        if ($has_event) {
            event(new ForgetSettingEvent($form));
        }

        cache()->forget('setting');
    }

    /**
     * set all settings
     *
     * @param array $value
     *
     * @return void
     */
    public function setAll(array $value): void
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
     * get form setting
     *
     * @param string $form
     *
     * @return array
     */
    public function form(string $form): array
    {
        $setting = [];
        foreach ($this->data as $key => $value) {
            if (str_starts_with($key, $form)) {
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

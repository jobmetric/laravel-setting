<?php

namespace JobMetric\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Facades\Setting as SettingFacade;
use JobMetric\Setting\Models\Setting as SettingModel;

class Setting
{
    /**
     * Dispatch setting
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
                    'form'    => $form,
                    'key'     => $key,
                    'value'   => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item,
                    'is_json' => is_array($item),
                ]);
            }
        }

        if ($has_event) {
            event(new StoreSettingEvent($form));
        }

        $this->buildCache();
    }

    /**
     * Forget setting
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

        cache()->forget(config('setting.cache_key'));
    }

    /**
     * Get setting
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Get form setting
     *
     * @param string $form
     *
     * @return array
     */
    public function form(string $form): array
    {
        return array_filter($this->all(), function ($key) use ($form) {
            return str_starts_with($key, $form);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Has setting key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * Get all setting keys
     *
     * @return array
     */
    public function all(): array
    {
        if (! Cache::has(config('setting.cache_key'))) {
            $this->buildCache();
        }

        return Cache::get(config('setting.cache_key'));
    }

    /**
     * Build setting cache
     *
     * @return void
     */
    private function buildCache(): void
    {
        Cache::remember(config('setting.cache_key'), config('setting.cache_time'), function () {
            $data = [];
            if (Schema::hasTable(config('setting.tables.setting'))) {
                $results = SettingModel::all();

                foreach ($results as $setting) {
                    $data[$setting->form . '_' . $setting->key] = ($setting->is_json) ? json_decode($setting->value, true) : $setting->value;
                }
            }

            return $data;
        });
    }
}

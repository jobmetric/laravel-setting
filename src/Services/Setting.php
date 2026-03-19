<?php

namespace JobMetric\Setting\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JobMetric\Form\Http\Requests\FormBuilderRequest;
use JobMetric\Setting\Contracts\AbstractSetting;
use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Exceptions\SettingClassInvalidException;
use JobMetric\Setting\Models\Setting as SettingModel;
use Throwable;

/**
 * Service for storing and retrieving settings from the settings table.
 *
 * @package JobMetric\Setting
 */
class Setting
{
    /**
     * Store settings from a setting class: validate request (or given input) with dto, then save. Form column =
     * application_key, key column = each field key, value = value, is_json = true when value is array.
     *
     * @param class-string<AbstractSetting> $class AbstractSetting class (e.g. ConfigSetting::class).
     * @param array<string, mixed>|null $input     Input to validate; defaults to request()->all().
     * @param bool $has_event
     *
     * @return array<string, mixed> Validated data that was stored.
     * @throws SettingClassInvalidException
     * @throws Throwable
     */
    public function dispatch(string $class, ?array $input = null, bool $has_event = true): array
    {
        if (! is_subclass_of($class, AbstractSetting::class)) {
            throw new SettingClassInvalidException($class);
        }

        $instance = app($class);
        $formName = $instance->formName();
        $input = $input ?? request()->all();
        $form = $instance->form()->build();
        $validated = dto($input, FormBuilderRequest::class, ['form' => $form]);

        $this->dispatchByForm($formName, $validated, $has_event);

        return $validated;
    }

    /**
     * Store or update settings for a form (form = application_key). Keys in $object must start with $form prefix; each
     * key column = field key, value column = value, is_json = true when value is array.
     *
     * @param string $form                 Form name (application_key) to store under.
     * @param array<string, mixed> $object Keys must start with $form prefix.
     * @param bool $has_event
     *
     * @return void
     */
    public function dispatchByForm(string $form, array $object, bool $has_event = true): void
    {
        $this->forget($form, $has_event);

        $batchData = [];
        $formPrefix = $form . '_';
        $formPrefixLength = strlen($formPrefix);
        $now = now();

        foreach ($object as $index => $item) {
            if (! str_starts_with((string) $index, $formPrefix)) {
                continue;
            }
            $key = substr((string) $index, $formPrefixLength);
            if ($key === '') {
                continue;
            }
            $isArray = is_array($item);
            $value = $isArray ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item;
            $batchData[] = [
                'form'       => $form,
                'key'        => $key,
                'value'      => $value,
                'is_json'    => $isArray,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($batchData !== []) {
            SettingModel::insert($batchData);
        }

        if ($has_event) {
            event(new StoreSettingEvent($form));
        }

        $this->buildCache();
    }

    /**
     * Remove all settings for a form from database and cache.
     *
     * @param string $form
     * @param bool $has_event
     *
     * @return void
     */
    public function forget(string $form, bool $has_event = true): void
    {
        SettingModel::ofForm($form)->delete();

        if ($has_event) {
            event(new ForgetSettingEvent($form));
        }

        $this->invalidateCache();
    }

    /**
     * Get a single setting value by key.
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
     * Get all settings for a form.
     *
     * @param string $form
     *
     * @return array<string, mixed>
     */
    public function form(string $form): array
    {
        return array_filter($this->all(), fn ($key) => str_starts_with((string) $key, $form), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Check if a setting key exists.
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
     * Get one field value by setting class and field key; or entire form when $fieldKey is null.
     *
     * @param class-string<AbstractSetting> $class
     * @param string|null $fieldKey Null to return full form array.
     * @param mixed $default        Used when $fieldKey is set and value is missing.
     *
     * @return mixed Single value, or array<string, mixed> when $fieldKey is null.
     * @throws SettingClassInvalidException
     */
    public function getFromClass(string $class, ?string $fieldKey = null, mixed $default = null): mixed
    {
        if (! is_subclass_of($class, AbstractSetting::class)) {
            throw new SettingClassInvalidException($class);
        }

        if ($fieldKey === null) {
            return $this->formFromClass($class);
        }

        $formName = app($class)->formName();
        $key = $formName . '_' . $fieldKey;

        return $this->get($key, $default);
    }

    /**
     * Invalidate settings cache (e.g. after manual DB change or in console).
     *
     * @return void
     */
    public function invalidateCache(): void
    {
        cache()->forget(config('setting.cache_key'));
    }

    /**
     * Check whether at least one record exists for the given form.
     *
     * @param string $form
     *
     * @return bool
     */
    public function exists(string $form): bool
    {
        return SettingModel::ofForm($form)->exists();
    }

    /**
     * Get multiple keys at once; each missing key gets $default. One cache read.
     *
     * @param array<int, string> $keys
     * @param mixed $default
     *
     * @return array<string, mixed>
     */
    public function getMany(array $keys, mixed $default = null): array
    {
        $all = $this->all();
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $all[$key] ?? $default;
        }

        return $result;
    }

    /**
     * Get all settings for a form by setting class.
     *
     * @param class-string<AbstractSetting> $class
     *
     * @return array<string, mixed>
     * @throws SettingClassInvalidException
     */
    public function formFromClass(string $class): array
    {
        if (! is_subclass_of($class, AbstractSetting::class)) {
            throw new SettingClassInvalidException($class);
        }

        $formName = app($class)->formName();

        return $this->form($formName);
    }

    /**
     * Get all settings from cache (builds cache if missing).
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if (! Cache::has(config('setting.cache_key'))) {
            $this->buildCache();
        }

        return Cache::get(config('setting.cache_key')) ?? [];
    }

    /**
     * Build and store settings in cache.
     *
     * @return void
     */
    private function buildCache(): void
    {
        Cache::remember(config('setting.cache_key'), config('setting.cache_time'), function () {
            $data = [];
            if (Schema::hasTable(config('setting.tables.setting'))) {
                $results = SettingModel::select(['form', 'key', 'value', 'is_json'])->get();
                foreach ($results as $setting) {
                    $data[$setting->form . '_' . $setting->key] = ($setting->is_json) ? json_decode($setting->value, true) : $setting->value;
                }
            }

            return $data;
        });
    }
}

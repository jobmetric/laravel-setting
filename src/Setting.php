<?php

namespace JobMetric\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JobMetric\Setting\Events\ForgetSettingEvent;
use JobMetric\Setting\Events\StoreSettingEvent;
use JobMetric\Setting\Facades\Setting as SettingFacade;
use JobMetric\Setting\Models\Setting as SettingModel;

/**
 * Class Setting
 *
 * Service class for managing application settings.
 * Responsibilities:
 * - Store and retrieve settings from database with caching
 * - Manage settings grouped by form (namespace)
 * - Handle JSON serialization/deserialization for complex values
 * - Fire domain events for setting changes
 * - Maintain cache invalidation on mutations
 */
class Setting
{
    /**
     * Store or update settings for a specific form (namespace).
     *
     * @param string $form    The form/namespace identifier (e.g., 'general', 'email')
     * @param array $object   Associative array of setting keys and values.
     *                        Keys must start with $form prefix (e.g., 'general_site_name').
     *                        Array values are automatically JSON-encoded.
     * @param bool $has_event Whether to fire StoreSettingEvent after storing (default: true)
     *
     * @return void
     */
    public function dispatch(string $form, array $object, bool $has_event = true): void
    {
        $this->forget($form, $has_event);

        // Prepare batch insert data
        $batchData = [];
        $formPrefix = $form . '_';
        $formPrefixLength = strlen($formPrefix);
        $now = now();

        foreach ($object as $index => $item) {
            if (str_starts_with($index, $formPrefix)) {
                // Extract key by removing form prefix
                $key = substr($index, $formPrefixLength);

                // Skip empty keys
                if (empty($key)) {
                    continue;
                }

                // Prepare value and determine if it's JSON
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
        }

        // Bulk insert if we have data
        if (! empty($batchData)) {
            SettingModel::insert($batchData);
        }

        if ($has_event) {
            event(new StoreSettingEvent($form));
        }

        $this->buildCache();
    }

    /**
     * Remove all settings for a specific form from database and cache.
     *
     * @param string $form    The form/namespace identifier to remove
     * @param bool $has_event Whether to fire ForgetSettingEvent after removal (default: true)
     *
     * @return void
     */
    public function forget(string $form, bool $has_event = true): void
    {
        // Get settings for cache unset (only select needed columns for performance)
        $settings = SettingModel::ofForm($form)->select(['form', 'key'])->get();

        // Unset from facade cache (in-memory operation, no database query)
        foreach ($settings as $setting) {
            SettingFacade::unset($setting->form . '_' . $setting->key);
        }

        // Bulk delete for better performance (single query instead of N queries)
        if ($settings->isNotEmpty()) {
            SettingModel::ofForm($form)->delete();
        }

        if ($has_event) {
            event(new ForgetSettingEvent($form));
        }

        cache()->forget(config('setting.cache_key'));
    }

    /**
     * Retrieve a single setting value by key.
     *
     * Role: provides convenient access to individual settings with default fallback.
     *
     * The key format should match the stored format: "form_key" (e.g., 'general_site_name').
     * If the key doesn't exist, returns the provided default value.
     * JSON-encoded values are automatically decoded when retrieved.
     *
     * @param string $key    The setting key in format "form_key"
     * @param mixed $default Default value to return if key doesn't exist (default: null)
     *
     * @return mixed The setting value, or default if not found
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Retrieve all settings that belong to a specific form (namespace).
     *
     * Role: filters all settings to return only those matching a form prefix.
     *
     * Returns an associative array of all settings where the key starts with
     * the form prefix (e.g., 'general' returns all 'general_*' settings).
     * JSON-encoded values are automatically decoded.
     *
     * @param string $form The form/namespace identifier to filter by
     *
     * @return array<string,mixed> Filtered array of settings for the form
     */
    public function form(string $form): array
    {
        return array_filter($this->all(), function ($key) use ($form) {
            return str_starts_with($key, $form);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Check if a setting key exists in the cache.
     *
     * Role: provides a boolean check for setting existence without retrieving value.
     *
     * @param string $key The setting key in format "form_key" to check
     *
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * Retrieve all settings from cache, building cache if needed.
     *
     * Role: provides access to the complete settings collection with lazy cache building.
     *
     * Returns an associative array where keys are in format "form_key" and values
     * are the setting values (JSON arrays/objects are automatically decoded).
     * If cache doesn't exist, automatically triggers buildCache() to populate it.
     *
     * @return array<string,mixed> All settings keyed by "form_key" format
     */
    public function all(): array
    {
        if (! Cache::has(config('setting.cache_key'))) {
            $this->buildCache();
        }

        return Cache::get(config('setting.cache_key'));
    }

    /**
     * Build and cache all settings from database.
     *
     * Role: populates the settings cache with data from database, handling JSON decoding.
     *
     * Process:
     * 1. Checks if settings table exists (handles cases where migrations haven't run)
     * 2. Retrieves only needed columns from database (form, key, value, is_json)
     * 3. Builds associative array with keys in format "form_key"
     * 4. Automatically decodes JSON values (when is_json flag is true) to arrays/objects
     * 5. Stores in cache with TTL from config('setting.cache_time')
     *
     * Cache key and TTL are configured via 'setting.cache_key' and 'setting.cache_time' config values.
     * This method is called automatically when cache is missing or after mutations.
     *
     * Performance: Uses select() to fetch only needed columns instead of all(), reducing memory usage.
     *
     * @return void
     */
    private function buildCache(): void
    {
        Cache::remember(config('setting.cache_key'), config('setting.cache_time'), function () {
            $data = [];
            if (Schema::hasTable(config('setting.tables.setting'))) {
                // Select only needed columns for better performance
                $results = SettingModel::select(['form', 'key', 'value', 'is_json'])->get();

                foreach ($results as $setting) {
                    $data[$setting->form . '_' . $setting->key] = ($setting->is_json) ? json_decode($setting->value, true) : $setting->value;
                }
            }

            return $data;
        });
    }
}

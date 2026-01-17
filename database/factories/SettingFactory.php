<?php

namespace JobMetric\Setting\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Setting\Models\Setting;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form'    => $this->faker->word,
            'key'     => $this->faker->word,
            'value'   => $this->faker->word,
            'is_json' => false,
        ];
    }

    /**
     * set form
     *
     * @param string $form
     *
     * @return static
     */
    public function setForm(string $form): static
    {
        return $this->state(fn (array $attributes) => [
            'form' => $form,
        ]);
    }

    /**
     * set key
     *
     * @param string $key
     *
     * @return static
     */
    public function setKey(string $key): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
        ]);
    }

    /**
     * set value
     *
     * @param string|array|null $value
     *
     * @return static
     */
    public function setValue(string|array|null $value): static
    {
        return $this->state(fn (array $attributes) => [
            'value'   => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
            'is_json' => is_array($value),
        ]);
    }
}

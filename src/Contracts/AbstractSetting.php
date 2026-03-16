<?php

namespace JobMetric\Setting\Contracts;

use JobMetric\Form\FormBuilder;
use Throwable;

/**
 * Base class for each setting form. One class per form (e.g. ConfigSetting, SystemSetting); programmer fills
 * application, key, title, description and builds the form via form(). Form key for storage is application_key.
 * Like Extension, form() returns FormBuilder.
 *
 * @package JobMetric\Setting
 */
abstract class AbstractSetting
{
    /**
     * Application or product name (e.g. app). First part of the form key; combined with key() for storage.
     *
     * @return string
     */
    abstract public function application(): string;

    /**
     * Unique key for this setting form (e.g. config, system). Second part of the form key; combined with application()
     * for storage.
     *
     * @return string
     */
    abstract public function key(): string;

    /**
     * Human-readable title for this form.
     *
     * @return string
     */
    abstract public function title(): string;

    /**
     * Short description of this form.
     *
     * @return string
     */
    abstract public function description(): string;

    /**
     * Form key for storage and Setting facade. Built from application() and key() (e.g. app_config). Passed to
     * Setting::dispatch(), Setting::form().
     *
     * @return string
     */
    public function formName(): string
    {
        return $this->application() . '_' . $this->key();
    }

    /**
     * Build the form definition (fields) for this setting form. Same role as Extension::form().
     *
     * @return FormBuilder
     */
    abstract public function form(): FormBuilder;

    /**
     * Return application, key, title, description, formName and form definition as array (for listing/API).
     *
     * @return array{application: string, key: string, title: string, description: string, form_name: string, form:
     *                            array<string, mixed>}
     * @throws Throwable
     */
    public function toArray(): array
    {
        return [
            'application' => $this->application(),
            'key'         => $this->key(),
            'title'       => $this->title(),
            'description' => $this->description(),
            'form_name'   => $this->formName(),
            'form'        => $this->form()->build()->toArray(),
        ];
    }
}

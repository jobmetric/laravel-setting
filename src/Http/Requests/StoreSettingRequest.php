<?php

namespace JobMetric\Setting\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Form\Http\Requests\FormTypeObjectRequest;
use JobMetric\Setting\Facades\SettingType;
use Throwable;

class StoreSettingRequest extends FormRequest
{
    use FormTypeObjectRequest;

    public array $data = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     * @throws Throwable
     */
    public function rules(): array
    {
        if (!empty(request()->all())) {
            $this->data = request()->all();
        }

        $parameters = request()->route()->parameters();
        $type = $parameters['type'] ?? null;

        $rules = [];

        // check type
        SettingType::checkType($type);

        $settingType = SettingType::type($type);

        $this->renderFormFiled($rules, $settingType->getForm());

        return $rules;
    }

    /**
     * Set data for validation
     *
     * @param array $data
     * @return static
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     * @throws Throwable
     */
    public function attributes(): array
    {
        if (!empty(request()->all())) {
            $this->data = request()->all();
        }

        $parameters = request()->route()->parameters();
        $type = $parameters['type'] ?? null;

        // check type
        SettingType::checkType($type);

        $settingType = SettingType::type($type);

        $params = [];

        $this->renderFormAttribute($params, $settingType->getForm());

        return $params;
    }
}

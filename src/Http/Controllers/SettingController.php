<?php

namespace JobMetric\Setting\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use JobMetric\Panelio\Facades\Breadcrumb;
use JobMetric\Panelio\Facades\Button;
use JobMetric\Panelio\Http\Controllers\Controller;
use JobMetric\Setting\Facades\Setting;
use JobMetric\Setting\Facades\SettingType;
use JobMetric\Setting\Http\Requests\StoreSettingRequest;
use Throwable;

class SettingController extends Controller
{
    private array $route;

    public function __construct()
    {
        if (request()->route()) {
            $parameters = request()->route()->parameters();

            $this->route = [
                'index' => route('setting.{type}.index', $parameters),
                'store' => route('setting.{type}.store', $parameters),
            ];
        }
    }

    /**
     * Display setting form.
     *
     * @param string $panel
     * @param string $section
     * @param string $type
     *
     * @return View|JsonResponse
     * @throws Throwable
     */
    public function index(string $panel, string $section, string $type): View|JsonResponse
    {
        $serviceType = SettingType::type($type);

        $data['label'] = $serviceType->getLabel();
        $data['description'] = $serviceType->getDescription();
        $data['form'] = $serviceType->getForm();
        $data['customFields'] = $data['form']->getAllCustomFields();

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($data['label']);

        // add button
        Button::save();

        DomiPlugins('jquery.form');

        DomiScript('assets/vendor/setting/js/form.js');

        $data['settingValues'] = Setting::form($type);

        return view('setting::form', $data);
    }

    /**
     * Store setting.
     *
     * @param StoreSettingRequest $request
     * @param string $panel
     * @param string $section
     * @param string $type
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(StoreSettingRequest $request, string $panel, string $section, string $type): RedirectResponse
    {
        Setting::dispatch($type, $request->validated());

        $this->alert(trans('setting::base.messages.saved', ['type' => SettingType::type($type)->getLabel()]));

        return back();
    }
}

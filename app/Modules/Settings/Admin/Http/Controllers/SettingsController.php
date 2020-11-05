<?php

namespace App\Modules\Settings\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Settings\Services\SettingsService;
use App\Modules\Settings\Models\Settings;
use App\Modules\Settings\Admin\Http\Requests\Settings\{
    IndexRequest,
    UpdateRequest
};

/**
 * @group USER
 */
class SettingsController extends AdminController
{
    /**
     * var SettingsService
     */
    protected $settingsService;

    /**
     * @param SettingsService     $settingsService
     */
    public function __construct(
        SettingsService $settingsService
    )
    {
        $this->settingsService = $settingsService;
    }

    public function index(IndexRequest $request)
    {
        $settings = new Settings;
        foreach (Settings::get() as $item) {
            $settings->{$item->key} = (int)$item->value;
        }

        return $this->view('settings.index', compact('settings'));
    }

    /**
     * Settings update
     *
     * @param   UpdateRequest $request
     */
    public function update(UpdateRequest $request)
    {
        $this->settingsService->update($request->all());

        return redirect()->back()
            ->with('success', __('settings::settings.success.updated'));
    }

}

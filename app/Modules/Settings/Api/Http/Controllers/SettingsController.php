<?php

namespace App\Modules\Settings\Api\Http\Controllers;

use App\Modules\Settings\Models\Settings;
use Illuminate\Http\JsonResponse;
use App\Base\ApiController;

/**
 * @group  settings
 */
class SettingsController extends ApiController
{
    /**
     * Settings get about biocad
     *
     * @authenticated
     * @responseFile  200 responses/settings/about/200.json
     *
     * @return    JsonResponse
     */
    public function about(): JsonResponse
    {
        $modelContent = Settings::where('key', 'about')->first();
        $modelTitle = Settings::where('key', 'about_title')->first();

        return response()->json([
            'title' => $modelTitle ? $modelTitle->value : '',
            'content' => $modelContent ? $modelContent->value : '',
        ]);
    }
}

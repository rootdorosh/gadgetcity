<?php

namespace App\Modules\Settings\Services;

use App\Services\Image\ImageService;
use App\Modules\Settings\Models\Settings;

/**
 * Class SettingsService
 */
class SettingsService
{
    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * UserCrudService constructor.
     *
     * @param ImageManagerInterface $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /*
     * @param   array $data
     */
    public function update(array $data)
    {
        Settings::getQuery()->delete();

        foreach ($data as $key => $value) {
            Settings::updateOrCreate(compact('key'), compact('key', 'value'));
        }
    }
}

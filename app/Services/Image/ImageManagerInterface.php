<?php
declare( strict_types = 1 );

namespace App\Services\Image;

use Illuminate\Http\UploadedFile;

/**
 * Interface ImageManagerInterface
 * @package App\Services\Image
 */
interface ImageManagerInterface
{
    /**
     * @param UploadedFile $imageFile
     * @param string       $name
     * @return string
     */
    public function save($data, string $imageName = null) : string;
}

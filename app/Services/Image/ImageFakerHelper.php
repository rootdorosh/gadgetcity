<?php
declare( strict_types = 1 );

namespace App\Services\Image;

/**
 * Class ImageFakerHelper
 * @package App\Services\Image
 */
class ImageFakerHelper
{
    /**
     * @param string  $path
     * @return string
     */
    public static function pathToRelative($path) : string
    {
        $expl = explode('uploads', (string)$path);
        
        return str_replace('\\', '/', end($expl));
    }
}

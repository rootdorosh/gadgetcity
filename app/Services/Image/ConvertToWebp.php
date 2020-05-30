<?php
declare( strict_types = 1 );

namespace App\Services\Image;

use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Class ConvertToWebp
 * @package App\Services\Image
 */
class ConvertToWebp
{
    /*
     * Source filename
     * 
     * @var string
     */
    private $source;
    
    /**
     * @param string $source
     * @return void
     */
    public function handle(string $source)
    {
        $ext = Str::afterLast($source, '.');
        $destination = str_replace(".{$ext}", '.webp', $source);
        
        (new ImageManager)->make($source)
            ->encode('webp')
            ->save($destination);
    }
}
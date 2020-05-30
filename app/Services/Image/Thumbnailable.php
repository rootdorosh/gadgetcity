<?php

namespace App\Services\Image;

use Illuminate\Support\Str;
use App;
use App\Services\Storage\ImageStorageManager;
use App\Services\Image\Thumb\Image;

trait Thumbnailable
{
    /**
     * Get image thumb
     *
     * @param  string $attribute
     * @param  int|null $width
     * @param  int|null $height
     * @param  string $mode
     * @return string|null
     */
    public function getThumb(
        string $attribute = 'image', 
        int $width = null, 
        int $height = null, 
        string $mode = 'resize'
    ): ? string
    {
        //$siteUrl = config('app.url');
        $siteUrl = '';
        
        if (!empty($this->$attribute)) {
            $image = $this->$attribute;
            $width = $width === null ? $height : $width;
            $height = $height === null ? $width : $height;
            
            $expl = explode('.', $image);

            if (end($expl) == 'svg' || (!$width && !$height)) {
                return $siteUrl . ImageStorageManager::UPLOAD_PATH . $image;
            }

            $basePath = public_path() . ImageStorageManager::UPLOAD_PATH;
            if (!is_file($basePath . $image)) {
                return null;
            }
                
            $pathInfo = pathinfo($basePath . $image);
            
            $pos = strrpos($image, "/");
            $fileMode = $mode == 'resize' ? 'r' : 'c';

            $thumbName = substr($image, 0, $pos) . '/' . $pathInfo['filename'] . '-' .
                $fileMode . '_' . $width . 'x' . $height . '.' . $pathInfo['extension'];
            
            if (!is_file($basePath . $thumbName)) {
                
				$thumb = new Image($basePath . $image);
				
                if ($mode == 'resize') {
                    
                    $gis = getimagesize($basePath . $image);
                    $w = $gis[0];
                    $h = $gis[1];
                    $_w =  $width;
                    $_h =  $height;

                    if (!empty($width) && !empty($height) && ($width >= $w) && ($height >= $h)) {
                        return $this->uploadPath . $image;
                    }

                    if ($h > $w) {
                        $_w = null;
                        $_h = $height;
                    } else if ($h < $w) {
                        $_w = $width;
                        $_h = null;
                    } else if ($h == $w) {
                        $_w = $height;
                        $_h = $height;
                    }
                    
                    $thumb->resize($_w, $_h);
                } else {
                    $thumb->crop($width, $height);
                }
                
                $thumb->save($basePath . $thumbName);
            }
            
            return $siteUrl . ImageStorageManager::UPLOAD_PATH . $thumbName;
        }

        return null;
    }
    
    /**
     * Get image thumb
     *
     * @param  string $attribute
     * @param  int|null $width
     * @param  int|null $height
     * @param  string $mode
     * @return string|null
     */
    public function getThumbWebp(
        string $attribute = 'image', 
        int $width = null, 
        int $height = null, 
        string $mode = 'resize'
    ): ? string
    {
        //$siteUrl = config('app.url');
        $siteUrl = '';
        
        if (!empty($this->$attribute)) {
            $image = $this->$attribute;
            
            $ext = Str::afterLast($image, '.');
            $imageWebp = str_replace(".{$ext}", '.webp', $image);
            
            $width = $width === 0 ? $height : $width;
            $height = $height === 0 ? $width : $height;
           
            if ($ext === 'svg') {
                return $siteUrl . ImageStorageManager::UPLOAD_PATH . $image;
            }
            
            if ((!$width && !$height)) {
                return $siteUrl . ImageStorageManager::UPLOAD_PATH . $imageWebp;
            }
            
            $basePath = public_path() . ImageStorageManager::UPLOAD_PATH;
            if (!is_file($basePath . $imageWebp)) {
                return null;
            }
            ////////////////////////////////////////////////////////////////////
                
            $pathInfo = pathinfo($basePath . $image);
            
            $pos = strrpos($image, "/");
            $fileMode = $mode == 'resize' ? 'r' : 'c';

            $thumbName = substr($image, 0, $pos) . '/' . $pathInfo['filename'] . '-' .
                $fileMode . '_' . $width . 'x' . $height . '.' . $pathInfo['extension'];
            
            if (!is_file($basePath . $thumbName)) {
				$thumb = new Image($basePath . $image);
				
                if ($mode == 'resize') {
                    
                    $gis = getimagesize($basePath . $image);
                    $w = $gis[0];
                    $h = $gis[1];
                    $_w =  $width;
                    $_h =  $height;

                    if (!empty($width) && !empty($height) && ($width >= $w) && ($height >= $h)) {
                        return $this->uploadPath . $image;
                    }

                    if ($h > $w) {
                        $_w = null;
                        $_h = $height;
                    } else if ($h < $w) {
                        $_w = $width;
                        $_h = null;
                    } else if ($h == $w) {
                        $_w = $height;
                        $_h = $height;
                    }
                    
                    $thumb->resize($_w, $_h);
                } else {
                    $thumb->crop($width, $height);
                }
                
                $thumb->save($basePath . $thumbName);
            }
            
            $thumbNameWebp = str_replace(".{$ext}", '.webp', $thumbName);
            if (!is_file(public_path() . ImageStorageManager::UPLOAD_PATH . $thumbNameWebp)) {
                (new ConvertToWebp)->handle(public_path() . ImageStorageManager::UPLOAD_PATH . $thumbName);
            }
            
            return $siteUrl . ImageStorageManager::UPLOAD_PATH . $thumbNameWebp;
        }

        return null;
    }
    
}

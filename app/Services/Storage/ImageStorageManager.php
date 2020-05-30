<?php
declare( strict_types = 1 );

namespace App\Services\Storage;

use App\Services\Storage\lib\StorageManager;
use Illuminate\Support\Str;

/**
 * Class ImageStorageManager
 * @package App\Services\Storage
 */
class ImageStorageManager extends StorageManager
{
    const UPLOAD_PATH = '/uploads';
    
    /**
     * @return string
     */
    public function getStoragePath() : string
    {
        return strtolower(Str::random(3)) . '/' . strtolower(Str::random(3)) . '/' . strtolower(Str::random(3)) . '/';
    }
}

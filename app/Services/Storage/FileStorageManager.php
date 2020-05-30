<?php
declare( strict_types = 1 );

namespace App\Services\Storage;

use App\Services\Storage\lib\StorageManager;
use Illuminate\Http\UploadedFile;

/**
 * Class FileStorageManager
 * @package App\Services\Storage
 */
class FileStorageManager extends StorageManager
{
    /**
     * @return string
     */
    public function getStoragePath() : string
    {
        return date('y') . '/' . date('m') . '/' . date('d') . '/';
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveByMd5(UploadedFile $file) : string
    {
        $fileName = md5((string) $file->getSize()) . '.' . $file->extension();

        return $this->save($file->get(), $fileName);
    }
}

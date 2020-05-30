<?php
declare( strict_types = 1 );

namespace App\Services\Storage\lib;

use Illuminate\Filesystem\FilesystemAdapter;

/**
 * Class StorageManagerHandler
 * @package App\Services\Storage\lib
 */
class StorageManagerHandler
{
    /**
     * @return FilesystemAdapter
     */
    public function getManager()
    {
        return \Storage::disk(env('STORAGE_DISK'));
    }
}

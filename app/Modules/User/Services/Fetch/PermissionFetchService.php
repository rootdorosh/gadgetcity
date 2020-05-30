<?php

namespace App\Modules\User\Services\Fetch;

use App\Modules\User\Models\Permission;

/**
 * Class PermissionFetchService
 */
class PermissionFetchService
{    
    /**
     * @return array
     */
    public static function getList(): array
    {
        return Permission::get()->pluck('slug', 'id')->toArray();
    }   
}

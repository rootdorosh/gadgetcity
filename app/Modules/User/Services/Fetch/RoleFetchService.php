<?php

namespace App\Modules\User\Services\Fetch;

use App\Modules\User\Models\Role;

/**
 * Class RoleFetchService
 */
class RoleFetchService
{    
    /**
     * @return array
     */
    public static function getList(): array
    {
        return Role::get()->pluck('name', 'id')->toArray();
    }   
}

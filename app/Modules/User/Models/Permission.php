<?php

namespace App\Modules\User\Models;

use jeremykenedy\LaravelRoles\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'users_permissions';
}

<?php

namespace App\Modules\User\Models;

use jeremykenedy\LaravelRoles\Models\Role as BaseRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Role extends BaseRole
{
    const ROLE_ADMIN    = 'admin';
    const ROLE_USER     = 'user';

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'users_roles';
    
    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Modules\User\Models\Permission',
            'users_permissions_vs_roles'
        );
    }
    
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAdmin(Builder $query) : Builder
    {
        return $query->where('slug', self::ROLE_ADMIN);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUser(Builder $query) : Builder
    {
        return $query->where('slug', self::ROLE_USER);
    }
    
}

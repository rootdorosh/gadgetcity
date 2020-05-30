<?php

namespace App\Modules\User\Services\Crud;

use App\Modules\User\Models\Role;

/**
 * Class RoleCrudService
 */
class RoleCrudService
{
    /*
     * @param   array $data
     * @return  Role
     */
    public function store(array $data): Role
    {
        $role = Role::create($data);
        $role->permissions()->sync(!empty($data['permissions']) ? $data['permissions'] : []);
        
        return $role;
    }

    /*
     * @param   User $role
     * @param   Role $data
     * @return  Role
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);
        $role->permissions()->sync(!empty($data['permissions']) ? $data['permissions'] : []);
        
        return $role;
    }

    /*
     * @param   Role $role
     * @return  void
     */
    public function destroy(Role $role): void
    {
        $role->delete();
    }
    
    /*
     * @param   array $ids
     * @return  void
     */
    public function bulkDestroy(array $ids): void
    {
        foreach ($ids as $id) {
            Role::find($id)->forceDelete();
        }
    }
}

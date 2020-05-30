<?php

namespace App\Modules\User\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\User\Services\Crud\RoleCrudService;
use App\Modules\User\Models\Role;
use App\Modules\User\Admin\Http\Requests\Role\{
    IndexFilter,
    FormRequest,
    CreateRequest,
    EditRequest,
    DestroyRequest,
    BulkDestroyRequest
};

/**
 * @group RoleController
 */
class RoleController extends AdminController
{
    /*
     * var RoleCrudService
     */
    protected $crudService;
    
    /*
     * @param RoleCrudService     $crudService
     */
    public function __construct(RoleCrudService $crudService)
    {
        $this->crudService = $crudService;
    }
    
    /**
     * Users list
     *
     * @param   IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }
        
        return $this->view('role.index', compact('modelFilter'));
    }

    /**
     * Role create
     *
     * @param   CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $role = new Role;
        
        return $this->view('role.create', compact('role'));       
    }

    /**
     * Role store
     *
     * @param   FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $role = $this->crudService->store($request->validated());
        
        return redirect(r('admin.user.roles.index'))
            ->with('success', __('user::role.success.created'));       
    }

    /**
     * Role edit
     *
     * @param   Role $role
     * @param   EditRequest $request
     */
    public function edit(Role $role, EditRequest $request)
    {
        return $this->view('role.update', compact('role'));       
    }

    /**
     * Role update
     *
     * @param   Role $role
     * @param   FormRequest $request
     */
    public function update(Role $role, FormRequest $request)
    {
        $role = $this->crudService->update($role, $request->validated());
        
        return redirect(r('admin.user.roles.index')) 
            ->with('success', __('user::role.success.updated'));       
    }

    /**
     * Role destroy
     *
     * @param   DestroyRequest $request
     * @param   Role $role
     */
    public function destroy(Role $role, DestroyRequest $request)
    {
        $this->crudService->destroy($role);
        
        return response()->json(null, 204);
    }
    
    /**
     * Users bulk destroy
     *
     * @param   BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);
        
        return response()->json(null, 204);
    }    
}
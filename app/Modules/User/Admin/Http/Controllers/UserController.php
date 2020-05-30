<?php

namespace App\Modules\User\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\User\Services\Crud\UserCrudService;
use App\Modules\User\Services\Fetch\RoleFetchService;
use App\Modules\User\Models\User;
use App\Modules\User\Admin\Http\Requests\User\{
    IndexFilter,
    FormRequest,
    CreateRequest,
    EditRequest,
    DestroyRequest,
    BulkDestroyRequest,
    BulkToggleRequest
};

/**
 * @group USER
 */
class UserController extends AdminController
{
    /*
     * var UserCrudService
     */
    protected $crudService;
    
    /*
     * var RoleFetchService
     */
    protected $roleFetchService;
        
    /*
     * @param UserCrudService     $crudService
     * @param RoleFetchService    $crudService
     */
    public function __construct(
        UserCrudService $crudService,
        RoleFetchService $roleFetchService
    )
    {
        $this->crudService = $crudService;
        $this->roleFetchService = $roleFetchService;
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
        
        return $this->view('user.index', compact('modelFilter'));
    }

    /**
     * User create
     *
     * @param   CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $user = new User;
        
        return $this->view('user.create', compact('user'));       
    }

    /**
     * User store
     *
     * @param   FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $user = $this->crudService->store($request->validated());
        
        return redirect(r('admin.user.users.index'))
            ->with('success', __('user::user.success.created'));       
    }

    /**
     * User edit
     *
     * @param   User $user
     * @param   EditRequest $request
     */
    public function edit(User $user, EditRequest $request)
    {
        return $this->view('user.update', compact('user'));       
    }

    /**
     * User update
     *
     * @param   User $user
     * @param   FormRequest $request
     */
    public function update(User $user, FormRequest $request)
    {
        $user = $this->crudService->update($user, $request->validated());
        
        return redirect(r('admin.user.users.index')) 
            ->with('success', __('user::user.success.updated'));       
    }

    /**
     * User destroy
     *
     * @param   DestroyRequest $request
     * @param   User $user
     */
    public function destroy(User $user, DestroyRequest $request)
    {
        $this->crudService->destroy($user);
        
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
    
    /**
     * Users bulk toggle attribute
     *
     * @param   BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());
        
        return response()->json(null, 204);
    }
}
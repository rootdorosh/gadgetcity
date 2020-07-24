<?php

namespace App\Modules\Product\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Product\Services\Crud\ProviderCrudService;
use App\Modules\Product\Models\Provider;
use App\Modules\Product\Admin\Http\Requests\Provider\{
    IndexFilter,
    FormRequest,
    CreateRequest,
    EditRequest,
    DestroyRequest,
    BulkDestroyRequest,
    BulkToggleRequest
};

/**
 */
class ProviderController extends AdminController
{
    /*
     * var ProviderCrudService
     */
    protected $crudService;

    /*
     * @param  ProviderCrudService     $crudService
     */
    public function __construct(ProviderCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * Providers list
     *
     * @param      IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('provider.index', compact('modelFilter'));
    }

    /**
     * Provider create
     *
     * @param      CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $provider = new Provider;

        return $this->view('provider.create', compact('provider'));
    }

    /**
     * Provider store
     *
     * @param      FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $provider = $this->crudService->store($request->validated());

        return redirect(r('admin.product.providers.index'))
            ->with('success', __('product::provider.success.created'));
    }

    /**
     * Provider edit
     *
     * @param      Provider $provider
     * @param      EditRequest $request
     */
    public function edit(Provider $provider, EditRequest $request)
    {
        return $this->view('provider.update', compact('provider'));
    }

    /**
     * Provider update
     *
     * @param      Provider $provider
     * @param      FormRequest $request
     */
    public function update(Provider $provider, FormRequest $request)
    {
        $provider = $this->crudService->update($provider, $request->validated());

        return redirect(r('admin.product.providers.index'))
            ->with('success', __('product::provider.success.updated'));
    }

    /**
     * Provider destroy
     *
     * @param      DestroyRequest $request
     * @param      Provider $provider
     */
    public function destroy(Provider $provider, DestroyRequest $request)
    {
        $this->crudService->destroy($provider);

        return response()->json(null, 204);
    }

    /**
     * Providers bulk destroy
     *
     * @param      BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);

        return response()->json(null, 204);
    }

    /**
     * Providers bulk toggle attribute
     *
     * @param      BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());

        return response()->json(null, 204);
    }
}

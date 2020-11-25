<?php

namespace App\Modules\Pattern\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Pattern\Services\Crud\PatternCrudService;
use App\Modules\Pattern\Models\Pattern;
use App\Services\Parser\ParserService;
use App\Modules\Pattern\Admin\Http\Requests\Pattern\{
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
class PatternController extends AdminController
{
    /*
     * var PatternCrudService
     */
    protected $crudService;

    /*
     * @param  PatternCrudService     $crudService
     */
    public function __construct(PatternCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * Patterns list
     *
     * @param      IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('pattern.index', compact('modelFilter'));
    }

    /**
     * Pattern create
     *
     * @param      CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $pattern = new Pattern;

        return $this->view('pattern.create', compact('pattern'));
    }

    /**
     * Pattern store
     *
     * @param      FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $pattern = $this->crudService->store($request->validated());

        return redirect(r('admin.pattern.patterns.index'))
            ->with('success', __('pattern::pattern.success.created'));
    }

    /**
     * Pattern edit
     *
     * @param      Pattern $pattern
     * @param      EditRequest $request
     */
    public function edit(Pattern $pattern, EditRequest $request)
    {
        return $this->view('pattern.update', compact('pattern'));
    }

    /**
     * Pattern update
     *
     * @param      Pattern $pattern
     * @param      FormRequest $request
     */
    public function update(Pattern $pattern, FormRequest $request)
    {
        $pattern = $this->crudService->update($pattern, $request->validated());

        return redirect(r('admin.pattern.patterns.index'))
            ->with('success', __('pattern::pattern.success.updated'));
    }

    /**
     * Pattern destroy
     *
     * @param      DestroyRequest $request
     * @param      Pattern $pattern
     */
    public function destroy(Pattern $pattern, DestroyRequest $request)
    {
        $this->crudService->destroy($pattern);

        return response()->json(null, 204);
    }

    /**
     * Patterns bulk destroy
     *
     * @param      BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);

        return response()->json(null, 204);
    }

    /**
     * Patterns bulk toggle attribute
     *
     * @param      BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());

        return response()->json(null, 204);
    }

    public function apply()
    {
        (new ParserService())->applyCustomTemplates();
        return redirect(r('admin.pattern.patterns.index'))
            ->with('success', __('pattern::pattern.success.apply'));
    }
}

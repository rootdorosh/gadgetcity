<?php 

namespace App\Modules\Color\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Color\Services\Crud\ColorCrudService;
use App\Modules\Color\Models\Color;
use App\Modules\Color\Admin\Http\Requests\Color\{
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
class ColorController extends AdminController
{
    /*
     * var ColorCrudService
     */
    protected $crudService;
            
    /*
     * @param  ColorCrudService     $crudService
     */
    public function __construct(ColorCrudService $crudService)
    {
        $this->crudService = $crudService;
    }
    
    /**
     * Colors list
     *
     * @param      IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }
        
        return $this->view('color.index', compact('modelFilter'));
    }

    /**
     * Color create
     *
     * @param      CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $color = new Color;
        
        return $this->view('color.create', compact('color'));       
    }

    /**
     * Color store
     *
     * @param      FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $color = $this->crudService->store($request->validated());
        
        return redirect(r('admin.color.colors.index'))
            ->with('success', __('color::color.success.created'));       
    }

    /**
     * Color edit
     *
     * @param      Color $color
     * @param      EditRequest $request
     */
    public function edit(Color $color, EditRequest $request)
    {
        return $this->view('color.update', compact('color'));       
    }

    /**
     * Color update
     *
     * @param      Color $color
     * @param      FormRequest $request
     */
    public function update(Color $color, FormRequest $request)
    {
        $color = $this->crudService->update($color, $request->validated());
        
        return redirect(r('admin.color.colors.index')) 
            ->with('success', __('color::color.success.updated'));       
    }

    /**
     * Color destroy
     *
     * @param      DestroyRequest $request
     * @param      Color $color
     */
    public function destroy(Color $color, DestroyRequest $request)
    {
        $this->crudService->destroy($color);
        
        return response()->json(null, 204);
    }
    
    /**
     * Colors bulk destroy
     *
     * @param      BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);
        
        return response()->json(null, 204);
    }
    
    /**
     * Colors bulk toggle attribute
     *
     * @param      BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());
        
        return response()->json(null, 204);
    }
}
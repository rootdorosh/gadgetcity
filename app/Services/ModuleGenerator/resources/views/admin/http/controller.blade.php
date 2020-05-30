namespace App\Modules\{{ $moduleName }}\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\{{ $moduleName }}\Services\Crud\{{ $model['name'] }}CrudService;
use App\Modules\{{ $moduleName }}\Models\{{ $model['name'] }};
use App\Modules\{{ $moduleName }}\Admin\Http\Requests\{{ $model['name'] }}\{
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
class {{ $model['name'] }}Controller extends AdminController
{
    /*
     * var {{ $model['name'] }}CrudService
     */
    protected $crudService;
            
    /*
     * @param {{ $model['name'] }}CrudService     $crudService
     */
    public function __construct({{ $model['name'] }}CrudService $crudService)
    {
        $this->crudService = $crudService;
    }
    
    /**
     * {{ $model['name'] }}s list
     *
     * @param   IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }
        
        return $this->view('{{ Str::camel($model['name']) }}.index', compact('modelFilter'));
    }

    /**
     * {{ $model['name'] }} create
     *
     * @param   CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        ${{ Str::camel($model['name']) }} = new {{ $model['name'] }};
        
        return $this->view('{{ Str::camel($model['name']) }}.create', compact('{{ Str::camel($model['name']) }}'));       
    }

    /**
     * {{ $model['name'] }} store
     *
     * @param   FormRequest $request
     */
    public function store(FormRequest $request)
    {
        ${{ Str::camel($model['name']) }} = $this->crudService->store($request->validated());
        
        return redirect(r('admin.{{ Str::kebab($moduleName) }}.{{ Str::kebab($model['name_plural']) }}.index'))
            ->with('success', __('{{ Str::snake($moduleName) }}::{{ Str::snake($model['name']) }}.success.created'));       
    }

    /**
     * {{ $model['name'] }} edit
     *
     * @param   {{ $model['name'] }} ${{ Str::camel($model['name']) }}
     * @param   EditRequest $request
     */
    public function edit({{ $model['name'] }} ${{ Str::camel($model['name']) }}, EditRequest $request)
    {
        return $this->view('{{ Str::camel($model['name']) }}.update', compact('{{ Str::camel($model['name']) }}'));       
    }

    /**
     * {{ $model['name'] }} update
     *
     * @param   {{ $model['name'] }} ${{ Str::camel($model['name']) }}
     * @param   FormRequest $request
     */
    public function update({{ $model['name'] }} ${{ Str::camel($model['name']) }}, FormRequest $request)
    {
        ${{ Str::camel($model['name']) }} = $this->crudService->update(${{ Str::camel($model['name']) }}, $request->validated());
        
        return redirect(r('admin.{{ Str::kebab($moduleName) }}.{{ Str::kebab($model['name_plural']) }}.index')) 
            ->with('success', __('{{ Str::snake($moduleName) }}::{{ Str::snake($model['name']) }}.success.updated'));       
    }

    /**
     * {{ $model['name'] }} destroy
     *
     * @param   DestroyRequest $request
     * @param   {{ $model['name'] }} ${{ Str::camel($model['name']) }}
     */
    public function destroy({{ $model['name'] }} ${{ Str::camel($model['name']) }}, DestroyRequest $request)
    {
        $this->crudService->destroy(${{ Str::camel($model['name']) }});
        
        return response()->json(null, 204);
    }
    
    /**
     * {{ $model['name'] }}s bulk destroy
     *
     * @param   BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);
        
        return response()->json(null, 204);
    }
    
    /**
     * {{ $model['name'] }}s bulk toggle attribute
     *
     * @param   BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());
        
        return response()->json(null, 204);
    }
}
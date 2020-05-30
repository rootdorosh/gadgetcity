<?php

namespace App\Modules\Product\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Product\Services\Crud\ProductCrudService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Admin\Http\Requests\Product\{
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
class ProductController extends AdminController
{
    /*
     * var ProductCrudService
     */
    protected $crudService;

    /*
     * @param  ProductCrudService     $crudService
     */
    public function __construct(ProductCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * Products list
     *
     * @param IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        //dd($modelFilter->getData());
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('product.index', compact('modelFilter'));
    }

    /**
     * Product create
     *
     * @param CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $product = new Product;

        return $this->view('product.create', compact('product'));
    }

    /**
     * Product store
     *
     * @param FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $product = $this->crudService->store($request->validated());

        return redirect(r('admin.product.products.index'))
            ->with('success', __('product::product.success.created'));
    }

    /**
     * Product edit
     *
     * @param Product $product
     * @param EditRequest $request
     */
    public function edit(Product $product, EditRequest $request)
    {
        return $this->view('product.update', compact('product'));
    }

    /**
     * Product update
     *
     * @param Product $product
     * @param FormRequest $request
     */
    public function update(Product $product, FormRequest $request)
    {
        $product = $this->crudService->update($product, $request->validated());

        return redirect(r('admin.product.products.index'))
            ->with('success', __('product::product.success.updated'));
    }

    /**
     * Product destroy
     *
     * @param DestroyRequest $request
     * @param Product $product
     */
    public function destroy(Product $product, DestroyRequest $request)
    {
        $this->crudService->destroy($product);

        return response()->json(null, 204);
    }

    /**
     * Products bulk destroy
     *
     * @param BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);

        return response()->json(null, 204);
    }

    /**
     * Products bulk toggle attribute
     *
     * @param BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());

        return response()->json(null, 204);
    }

    /**
     * Products autocomplete
     */
    public function autocomplete()
    {
        $data = [];
        $q = trim(request()->get('q'));
        foreach (Product::where('title', 'like', "%$q%")->limit(20)->get() as $product) {
            $data[] = [
                'id' => $product->id,
                'title' => $product->title,
            ];
        }

        return response()->json($data);
    }
}

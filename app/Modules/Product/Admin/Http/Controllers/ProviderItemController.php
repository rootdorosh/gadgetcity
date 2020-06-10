<?php

namespace App\Modules\Product\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\Crud\ProviderItemCrudService;
use App\Modules\Product\Models\ProviderItem;
use App\Modules\Product\Models\Price;
use App\Modules\Product\Admin\Http\Requests\ProviderItem\{
    IndexFilter,
    FormRequest,
    CreateRequest,
    EditRequest,
    DestroyRequest,
    BulkDestroyRequest,
    BulkToggleRequest
};
use Illuminate\Http\Request;

/**
 */
class ProviderItemController extends AdminController
{
    /*
     * var ProviderItemCrudService
     */
    protected $crudService;

    /*
     * @param  ProviderItemCrudService     $crudService
     */
    public function __construct(ProviderItemCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * ProviderItems list
     *
     * @param      IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        dd(app()->getLocale());

        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('providerItem.index', compact('modelFilter'));
    }

    /**
     * ProviderItem create
     *
     * @param      CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $providerItem = new ProviderItem;

        return $this->view('providerItem.create', compact('providerItem'));
    }

    /**
     * ProviderItem store
     *
     * @param      FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $providerItem = $this->crudService->store($request->validated());

        return redirect(r('admin.product.provider-items.index'))
            ->with('success', __('product::provider_item.success.created'));
    }

    /**
     * ProviderItem edit
     *
     * @param      ProviderItem $providerItem
     * @param      EditRequest $request
     */
    public function edit(ProviderItem $providerItem, EditRequest $request)
    {
        return $this->view('providerItem.update', compact('providerItem'));
    }

    /**
     * ProviderItem update
     *
     * @param      ProviderItem $providerItem
     * @param      FormRequest $request
     */
    public function update(ProviderItem $providerItem, FormRequest $request)
    {
        $providerItem = $this->crudService->update($providerItem, $request->validated());

        return redirect(r('admin.product.provider-items.index'))
            ->with('success', __('product::provider_item.success.updated'));
    }

    /**
     * ProviderItem destroy
     *
     * @param      DestroyRequest $request
     * @param      ProviderItem $providerItem
     */
    public function destroy(ProviderItem $providerItem, DestroyRequest $request)
    {
        $this->crudService->destroy($providerItem);

        return response()->json(null, 204);
    }

    /**
     * ProviderItems bulk destroy
     *
     * @param      BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);

        return response()->json(null, 204);
    }

    /**
     * Provider items bulk toggle attribute
     *
     * @param   Request $request
     */
    public function bulkToggle(Request $request)
    {
        $this->crudService->bulkToggle($request->all());

        return response()->json(null, 204);
    }

    /**
     * Provider items set product
     *
     * @param   Request $request
     */
    public function setProduct(Request $request)
    {
        $providerItem = ProviderItem::where('id', $request->id)->first();
        $providerItem->product_id = $request->product_id;
        $providerItem->status = ProviderItem::STATUS_ACCEPT;
        $providerItem->save();

        $attrs = [
            'product_id' => $providerItem->product_id,
            'provider_id' => $providerItem->provider_id,
        ];

        Price::updateOrCreate($attrs, array_merge($attrs, ['price' => $providerItem->price]));

        return response()->json([
            'product_title' => $providerItem->product->title,
            'status_title' => $providerItem->getStatusTitle(),
        ]);
    }

}

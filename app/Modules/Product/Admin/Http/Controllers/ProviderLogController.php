<?php

namespace App\Modules\Product\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Product\Models\ProductProviderPrice;
use App\Modules\Product\Models\ProviderLog;
use App\Modules\Product\Admin\Http\Requests\ProviderLog\{
    IndexFilter,
    DestroyRequest
};
use Illuminate\Http\Request;

/**
 */
class ProviderLogController extends AdminController
{
    /**
     * ProviderLogs list
     *
     * @param IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('providerLog.index', compact('modelFilter'));
    }

    /**
     * ProviderLog destroy
     *
     * @param      DestroyRequest $request
     * @param      ProviderLog $providerLog
     */
    public function destroy(ProviderLog $providerLog, DestroyRequest $request)
    {
        $this->crudService->destroy($providerLog);

        return response()->json(null, 204);
    }
}

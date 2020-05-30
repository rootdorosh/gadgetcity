<?php

namespace App\Base\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use App\Base\Requests\BaseFormRequest;
use App\Base\Requests\BaseIndexRequest;
use App\Base\Requests\BaseShowRequest;
use App\Base\Requests\BaseDestroyRequest;

/**
 *
 */
interface CrudControllerContract
{
    /*
     * @param   BaseIndexRequest $request
     * @return  JsonResponse
     */
    public function index(BaseIndexRequest $request): JsonResponse;

    /*
     * @param   BaseFormRequest $request
     * @return  JsonResponse
     */
    public function store(BaseFormRequest $request): JsonResponse;

    /*
     * @param   BaseFormRequest $request
     * @param   Model $model
     * @return  JsonResponse
     */
    public function update(Model $model, BaseFormRequest $request): JsonResponse;

    /*
     * @param   BaseShowRequest $request
     * @param   Model $model
     * @return  JsonResponse
     */
    public function show(Model $model, BaseShowRequest $request): JsonResponse;

    /*
     * @param   BaseDestroyRequest $request
     * @param   Model $model
     * @return  JsonResponse
     */
    public function destroy(Model $model, BaseDestroyRequest $request): JsonResponse;
}

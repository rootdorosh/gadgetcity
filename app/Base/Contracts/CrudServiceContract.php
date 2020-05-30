<?php

namespace App\Base\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
interface CrudServiceContract
{
    /*
     * @param array $data
     * 
     * @return Model
     */
    public function store(array $data): Model;

    /*
     * @param Model $model
     * @param array $data
     * 
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /*
     * @param Model $model
     * 
     * @return void
     */
    public function desctroy(Model $model): void;
}

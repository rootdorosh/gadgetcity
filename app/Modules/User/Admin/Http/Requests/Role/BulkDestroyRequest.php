<?php

namespace App\Modules\User\Admin\Http\Requests\Role;

/**
 * Class BulkDestroyRequest
 * 
 * @package App\Modules\User
 *
 * @bodyParam ids   array    required ids.
 * @bodyParam ids.* integer  required role id.
 */
class BulkDestroyRequest extends DestroyRequest
{
    /*
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids'   => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                'exists:users_roles,id',
            ],
        ];
    }
}

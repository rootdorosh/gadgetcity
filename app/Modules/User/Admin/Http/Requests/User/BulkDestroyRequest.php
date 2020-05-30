<?php

namespace App\Modules\User\Admin\Http\Requests\User;

/**
 * Class BulkDestroyRequest
 * 
 * @package App\Modules\User
 *
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
                'exists:users,id',
            ],
        ];
    }
}

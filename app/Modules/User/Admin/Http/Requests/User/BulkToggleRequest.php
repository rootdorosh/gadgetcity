<?php

namespace App\Modules\User\Admin\Http\Requests\User;

/**
 * Class BulkToggleRequest
 * 
 * @package App\Modules\User
 *
 */
class BulkToggleRequest extends DestroyRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('user.user.update');
    }    

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
            'attribute' => [
                'required',
                'string',
                'in:is_active',
            ],
            'value' => [
                'required',
                'integer',
                'in:0,1',
            ],
        ];
    }
}

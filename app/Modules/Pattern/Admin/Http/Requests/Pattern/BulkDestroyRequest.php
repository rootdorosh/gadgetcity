<?php 

namespace App\Modules\Pattern\Admin\Http\Requests\Pattern;

/**
 * Class BulkDestroyRequest
 * 
 * @package  App\Modules\Pattern
 *
 */
class BulkDestroyRequest extends DestroyRequest
{
    /*
     * @return  array
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
                'exists:pattern,id',
            ],
        ];
    }
}

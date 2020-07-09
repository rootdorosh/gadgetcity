<?php 

namespace App\Modules\Color\Admin\Http\Requests\Color;

/**
 * Class BulkDestroyRequest
 * 
 * @package  App\Modules\Color
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
                'exists:color,id',
            ],
        ];
    }
}

<?php 

namespace App\Modules\Product\Admin\Http\Requests\ProviderItem;

/**
 * Class BulkDestroyRequest
 * 
 * @package  App\Modules\Product
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
                'exists:product_providers_items,id',
            ],
        ];
    }
}

<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\ProviderItem;

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 * 
 * @package  App\Modules\Product
 *
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        $action = empty($this->provider_item) ? 'store' : 'update';
        
        return $this->user()->hasPermission('product.provideritem.' . $action);
    }
    
    /**
     * @return  array
     */
    public function rules(): array
    {
        $rules = [
            'provider_id' => [
                'required',
                'integer',
                'integer',
                'exists:product_providers,id',
            ],
            'title' => [
                'required',
                'string',
            ],
            'product_id' => [
                'required',
                'integer',
                'integer',
                'exists:product,id',
            ],
            'status' => [
                'required',
                'integer',
                'integer',
            ],
            'price' => [
                'required',
                'float',
            ],
        ];
                
        return $rules;
    }
    
    /*
     * @return  array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Product', 'ProviderItem');
    }
}
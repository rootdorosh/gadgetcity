<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Product;

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
        $action = empty($this->product) ? 'store' : 'update';
        
        return $this->user()->hasPermission('product.product.' . $action);
    }
    
    /**
     * @return  array
     */
    public function rules(): array
    {
        $rules = [
            'title' => [
                'required',
                'string',
            ],
            'is_active' => [
                'required',
                'integer',
                'in:0,1',
            ],
        ];
                
        return $rules;
    }
    
    /*
     * @return  array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Product', 'Product');
    }
}
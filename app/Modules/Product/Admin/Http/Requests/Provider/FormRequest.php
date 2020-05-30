<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Provider;

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
        $action = empty($this->provider) ? 'store' : 'update';
        
        return $this->user()->hasPermission('product.provider.' . $action);
    }
    
    /**
     * @return  array
     */
    public function rules(): array
    {
        $rules = [
            'pid' => [
                'required',
                'string',
            ],
            'title' => [
                'required',
                'string',
            ],
            'is_active' => [
                'required',
                'integer',
                'in:0,1',
            ],
            'last_guid' => [
                'nullable',
                'integer',
            ],
        ];
                
        return $rules;
    }
    
    /*
     * @return  array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Product', 'Provider');
    }
}
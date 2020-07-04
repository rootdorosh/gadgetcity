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
class ImportAvailabilityRequest extends BaseFormRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('product.product.index');
    }

    /**
     * @return  array
     */
    public function rules(): array
    {
        $rules = [
            'file_import' => [
                'required',
                'mimes:xlsx,xls',
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

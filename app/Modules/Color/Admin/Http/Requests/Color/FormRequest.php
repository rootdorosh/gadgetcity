<?php

declare( strict_types = 1 );

namespace App\Modules\Color\Admin\Http\Requests\Color;

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 *
 * @package  App\Modules\Color
 *
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        $action = empty($this->color) ? 'store' : 'update';

        return $this->user()->hasPermission('color.color.' . $action);
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
            'code' => [
                'required',
                'string',
                'unique:color',
            ],
        ];

        return $rules;
    }

    /*
     * @return  array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Color', 'Color');
    }
}

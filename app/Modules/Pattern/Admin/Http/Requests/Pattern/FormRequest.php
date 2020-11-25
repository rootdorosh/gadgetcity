<?php

declare( strict_types = 1 );

namespace App\Modules\Pattern\Admin\Http\Requests\Pattern;

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 *
 * @package  App\Modules\Pattern
 *
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        $action = empty($this->pattern) ? 'store' : 'update';

        return $this->user()->hasPermission('pattern.pattern.' . $action);
    }

    /**
     * @return  array
     */
    public function rules(): array
    {
        $rules = [
            'example' => [
                'required',
                'string',
            ],
            'value' => [
                'required',
                'string',
            ],
            'rank' => [
                'required',
                'integer',
            ],
        ];

        return $rules;
    }

    /**
     * @return  array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Pattern', 'Pattern');
    }

    /**
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!empty($this->example) && !empty($this->value)) {

                try {
                    if (!preg_match($this->value, $this->example, $match)) {
                        $validator->errors()->add('value', 'Нет совпадения');
                    }

                } catch (\Exception $e) {
                    $validator->errors()->add('value', $e->getMessage());
                }
            }
        });
    }

}

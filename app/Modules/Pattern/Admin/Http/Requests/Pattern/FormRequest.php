<?php

declare( strict_types = 1 );

namespace App\Modules\Pattern\Admin\Http\Requests\Pattern;

use App\Base\Requests\BaseFormRequest;
use App\Modules\Pattern\Models\Pattern;
use Illuminate\Validation\Rule;

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
        $pattern = $this->pattern;

        $rules = [
            'example' => [
                'required',
                'string',
                Rule::unique('pattern')->where(function ($query) use ($pattern) {
                    if ($pattern && !empty($pattern->id)) {
                        $query->where('id', '<>', $pattern->id);
                    }
                }),
            ],
            'value' => [
                'required',
                'string',
                Rule::unique('pattern')->where(function ($query) use ($pattern) {
                    if ($pattern && !empty($pattern->id)) {
                        $query->where('id', '<>', $pattern->id);
                    }
                }),
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
                $pattern = Pattern::convertToPattern($this->value);

                try {
                    if (!preg_match($pattern, $this->example, $match)) {
                        $validator->errors()->add('value', 'Нет совпадения: ' . $pattern);
                    }

                } catch (\Exception $e) {
                    $validator->errors()->add('value', $e->getMessage() . ': ' . $pattern);
                }
            }
        });
    }

}

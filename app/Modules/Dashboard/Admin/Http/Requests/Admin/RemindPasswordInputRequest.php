<?php
declare( strict_types = 1 );

namespace App\Modules\Auth\Http\Requests\Admin;

use App\Base\Requests\BaseFormRequest;
use App\Modules\Auth\Http\Validators\RemindCode;
use App\Modules\Auth\Http\Validators\UserActive;

/**
 * Class RemindPasswordInput
 * 
 * @package App\Modules\Auth
 */
class RemindPasswordInput extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'email',
                new UserActive($this),
            ],
            'code'    => [
                'required',
                'integer',
                new RemindCode($this),
            ],
            'password' => 'required|string|min:6|max:20',
        ];
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Auth', 'RemindPasswordInput');
    }
}

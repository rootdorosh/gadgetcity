<?php
declare( strict_types = 1 );

namespace App\Modules\Auth\Admin\Http\Requests;

use App\Base\Requests\BaseFormRequest;
use App\Modules\Auth\Admin\Http\Validators\UserActive;

/**
 * Class LoginRequest
 * 
 * @package App\Modules\Auth
 */
class LoginRequest extends BaseFormRequest
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
            'password' => 'required|string|min:5|max:20',
            
        ];
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Auth', 'LoginForm');
    }
    
}

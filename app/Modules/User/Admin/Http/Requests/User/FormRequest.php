<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\User;

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 * 
 * @package App\Modules\User
 *
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        $action = empty($this->user) ? 'store' : 'update';
        
        return allow('user.user.' . $action);
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'email' => [
                'required',
                'string',
                'email',
                !empty($this->user) ? 'unique:users,id,' . $this->user->id : 'unique:users',
            ],           
            'name' => [
                'required',
                'string',
            ],           
            'password' => [
                !empty($this->user) ? 'nullable' : 'required',
                'string',
                'min:8',
                'max:20',
            ],
            'is_active' => [
                'required',
                'integer',
                'in:0,1',
            ],                       
            'image_file' => [
                'nullable',
                'mimes:jpeg,jpg,png',
                'max:' . (1024 * 5), // 5MB
            ],                       
            'roles' => [
                'nullable',
                'array',
            ],
            'roles.*' => [
                'integer',
                'exists:users_roles,id',
            ],
        ];
                
        return $rules;
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('user', 'user');
    }
}

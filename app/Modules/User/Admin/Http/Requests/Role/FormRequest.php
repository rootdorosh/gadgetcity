<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\Role;

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 * 
 * @package App\Modules\User
 *
 * @bodyParam name          string   required name.
 * @bodyParam slug          string   required slug.
 * @bodyParam description   string   optional description.
 * @bodyParam permissions   array    optional permissions.
 * @bodyParam permissions.* integer  optional permissions item.
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        $action = empty($this->role) ? 'store' : 'update';
        
        return $this->user()->hasPermission('user.role.' . $action);
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'slug' => [
                'required',
                'string',
                !empty($this->role) ? 'unique:users_roles,id,' . $this->role->id : 'unique:users_roles',
            ],           
            'name' => [
                'required',
                'string',
                !empty($this->role) ? 'unique:users_roles,id,' . $this->role->id : 'unique:users_roles',
            ],           
            'description' => [
                'nullable',
                'string',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'integer',
                'exists:users_permissions,id',
            ],
        ];
                
        return $rules;
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('User', 'Role');
    }
}

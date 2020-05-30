<?php

namespace App\Modules\User\Admin\Http\Requests\Role;

use Illuminate\Database\Eloquent\Builder;
use App\Modules\User\Models\Role;
use App\Base\Requests\BaseFilter;
use DB;

class IndexFilter extends BaseFilter
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('user.role.index');
    }

    /*
     * @return array
     */
    public function rules() : array
    {
        return parent::rules() + [
            'sort_attr' => [
                'nullable',
                'in:' . implode(',', [
                    'id',
                    'email',
                    'name',
                    'is_active',
                ]),
            ],
            'id' => [
                'nullable',
                'string',
            ],
            'name' => [
                'nullable',
                'string',
            ],
            'slug' => [
                'nullable',
                'string',
            ],
        ];
    }
    
    /*
     * @return Builder
     */
    public function getQueryBuilder() : Builder
    {
        $query = Role::select([
            'users_roles.*',
        ]);
        
        foreach (['slug', 'name'] as $attr) {
            if (!empty($this->attr($attr))) {
                $query->where($attr, 'like', "%" . $this->attr($attr) . "%");
            }
        }
        
        return $query;
    }

    /*
     * @return array
     */
    public function getData()
    {
        return $this->resolveData(function($row) {
            return [
                'id' => $row->id,
                'slug' => $row->slug,
                'name' => $row->name,
                'permissions' => implode(' ', array_map(function ($v) {
                    return '<span class="badge badge-info">' . $v . '</span>';
                }, $row->permissions()->pluck('slug')->toArray()))
            ];            
        });
    }
}
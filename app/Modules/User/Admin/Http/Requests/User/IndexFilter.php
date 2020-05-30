<?php

namespace App\Modules\User\Admin\Http\Requests\User;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use DB;
use App\Base\Requests\BaseFilter;

class IndexFilter extends BaseFilter
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('user.user.index');
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
            'email' => [
                'nullable',
                'string',
            ],
            'name' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'in:0,1',
            ],
        ];
    }
    
    /*
     * @return Builder
     */
    public function getQueryBuilder() : Builder
    {
        $query = User::select([
            'users.*',
        ]);
        
        foreach (['id', 'email', 'name'] as $attr) {
            if (!empty($this->attr($attr))) {
                $query->where($attr, 'like', "%" . $this->attr($attr) . "%");
            }
        }
        if ($this->attr('is_active') !== null) {
            $query->where('is_active', $this->attr('is_active'));            
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
                'email' => $row->email,
                'is_active' => $row->is_active,
                'name' => $row->name,
                'roles' => implode(' ', array_map(function ($v) {
                    return '<span class="badge badge-info">' . $v . '</span>';
                }, $row->roles()->pluck('name')->toArray()))
            ];            
        });
    }
}
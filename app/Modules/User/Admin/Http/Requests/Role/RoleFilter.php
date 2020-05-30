<?php

namespace App\Forms;

use App\Role;
use Illuminate\Database\Eloquent\Builder;
use DB;

class RoleFilter extends Base
{
    /*
     * @return array
     */
    public function rules() : array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'sort_dir' => [
                'nullable',
                'in:desc,asc',
            ],
            'sort_attr' => [
                'nullable',
                'in:' . implode(',', [
                    'roles.id',
                    'roles.title',
                ]),
            ],
        ];
    }

    /*
     * @return Builder
     */
    public function getQueryBuilder() : Builder
    {
        $query = Role::select([
            'roles.*',
        ]);
       
        return $query;
    }

    /*
     * @return array
     */
    public function paginate()
    {
        $perPage = $this->attr('per_page', self::PER_PAGE);
        $page = $this->attr('page', self::PAGE);
        $sortDir = $this->attr('sort_dir');
        $sortAttr = $this->attr('sort_attr');
        $offset = $page * $perPage - $perPage;

        $query = $this->getQueryBuilder();
        $count = $query->count();

        $items = [];

        $query->offset($offset)->limit($perPage);
        if ($sortDir && $sortAttr) {
            $query->orderBy($sortAttr, $sortDir);
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            $items[] =  [
                'roles.id' => $row->id,
                'roles.title' => $row->title,
                'permissions' => implode(' ', array_map(function ($v) {
                    return '<span class="badge badge-info">' . $v . '</span>';
                }, $row->permissions()->pluck('title')->toArray()))
            ];
        }

        return [
            'items' => $items,
            'count' => $count,
            'from' => $offset + 1,
            'to' => $offset + min($offset + $perPage, $count),
        ];
    }
}

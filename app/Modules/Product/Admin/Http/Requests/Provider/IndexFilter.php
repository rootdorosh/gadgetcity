<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Provider;

use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Product\Models\Provider;

/**
 * Class IndexRequest
 * 
 * @package  App\Modules\Product
 *
 */
class IndexFilter extends BaseFilter
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('product.provider.index');
    }
        
    /*
     * @return  array
     */
    public function rules(): array
    {
        $rules = parent::rules() + [
            'sort_attr' => [
                'nullable',
                'string',
                'in:' . implode(',', [
                    'id',
                    'pid',
                    'title',
                    'is_active',
                ]),
            ],
            'pid' => [
                'nullable',
                'string',
            ],
            'title' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'integer',
            ],
            'id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
        
        return $rules;
    }
            
    /*
     * @return  Builder
     */
    public function getQueryBuilder() : Builder
    {
        $query = Provider::query();
        if ($this->id !== null) {
            $query->where("id", "like", "%{$this->id}%");
        }

        if ($this->pid !== null) {
            $query->where("pid", "like", "%{$this->pid}%");
        }

        if ($this->title !== null) {
            $query->where("title", "like", "%{$this->title}%");
        }

        if ($this->is_active !== null) {
            $query->where("is_active", "like", "%{$this->is_active}%");
        }
        
        return $query;
    }    
    
    /*
     * @return  array
     */
    public function getData()
    {
        return $this->resolveData(function($row) {
            return [
                'id' => $row->id,
                'pid' => $row->pid,
                'title' => $row->title,
                'is_active' => $row->is_active,
            ];            
        });
    }
    

}
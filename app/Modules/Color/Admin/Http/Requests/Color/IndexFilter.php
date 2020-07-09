<?php 

declare( strict_types = 1 );

namespace App\Modules\Color\Admin\Http\Requests\Color;

use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Color\Models\Color;

/**
 * Class IndexRequest
 * 
 * @package  App\Modules\Color
 *
 */
class IndexFilter extends BaseFilter
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('color.color.index');
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
                    'title',
                    'code',
                ]),
            ],
            'title' => [
                'nullable',
                'string',
            ],
            'code' => [
                'nullable',
                'string',
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
        $query = Color::query();
        if ($this->id !== null) {
            $query->where("id", "like", "%{$this->id}%");
        }

        if ($this->title !== null) {
            $query->where("title", "like", "%{$this->title}%");
        }

        if ($this->code !== null) {
            $query->where("code", "like", "%{$this->code}%");
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
                'title' => $row->title,
                'code' => $row->code,
            ];            
        });
    }
    

}
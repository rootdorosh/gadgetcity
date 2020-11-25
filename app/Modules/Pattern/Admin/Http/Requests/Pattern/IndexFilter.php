<?php

declare( strict_types = 1 );

namespace App\Modules\Pattern\Admin\Http\Requests\Pattern;

use Illuminate\Database\Eloquent\Builder;
use App\Base\Requests\BaseFilter;
use App\Modules\Pattern\Models\Pattern;

/**
 * Class IndexRequest
 *
 * @package  App\Modules\Pattern
 *
 */
class IndexFilter extends BaseFilter
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('pattern.pattern.index');
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
                    'example',
                    'value',
                    'rank',
                ]),
            ],
            'example' => [
                'nullable',
                'string',
            ],
            'value' => [
                'nullable',
                'string',
            ],
            'rank' => [
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
        $query = Pattern::query();
        if ($this->id !== null) {
            $query->where("id", "like", "%{$this->id}%");
        }

        if ($this->example !== null) {
            $query->where("example", "like", "%{$this->example}%");
        }

        if ($this->value !== null) {
            $query->where("value", "like", "%{$this->value}%");
        }

        if ($this->rank !== null) {
            $query->where("rank", "like", "%{$this->rank}%");
        }

        $query->orderBy('rank');

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
                'example' => $row->example,
                'value' => $row->value,
                'rank' => $row->rank,
                'result' => '<pre>' . $this->getResult($row) . '</pre>',
            ];
        });
    }

    /**
     * @param Pattern $pattern
     * @return string
     */
    public function getResult(Pattern $pattern): string
    {
        try {
            preg_match($pattern->value, $pattern->example, $match);
            return var_export($match, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


}

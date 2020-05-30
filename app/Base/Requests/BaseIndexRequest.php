<?php

namespace App\Base\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use DB;

abstract class BaseIndexRequest extends BaseRequest
{
    const PER_PAGE = 10;
    const PAGE = 1;

    /*
     * @var array
     */
    private $errors;

    /*
     * @return bool
     */
    abstract public function authorize(): bool;

    /*
     * @return Builder
     */
    abstract public function getQueryBuilder() : Builder;

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
        ];
    }  
   
    /*
     * @return array
     */
    public function attributes(): array
    {
        return [
            'page' => __('filter.page'),
            'per_page' => __('filter.per_page'),
            'sort_dir' => __('filter.sort_dir'),
        ];
    }
    

    /*
     * @return array
     */
    public function validationData()
    {
        return $this->queryParams();
    }

    /*
     * @return array
     */
    public function queryParams() : array
    {
        $data = [];
        foreach (array_keys(static::rules()) as $k) {
            $value = $this->$k;

            if (!is_array($value)) {
                $data[$k] = $this->resolveType($value);
            } else {
                foreach ($value as $v) {
                    $data[$k][] = $this->resolveType($v);
                }
            }
        }
        
        return $data;
    }

    /*
     * @param mixed $value
     * @return mixed
     */
    public function resolveType($value)
    {
        if ($value instanceof Collection) {
            return $value->pluck('id')->toArray();
        }
        
        $valueInt = $value;
        $valueFloat = $value;
        
        $valueInt = (int) $valueInt;
        $valueFloat = (float) $valueFloat;
        
        if ($value === null) {
            //return $value;
        } elseif ((string)$valueInt == $value) {
            return $valueInt;
        } elseif ((string)$valueFloat == $value) {
            return $valueFloat;
        } else {
            return $value;
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('validation.the_given_data_was_invalid'),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get validation errors.
     *
     * @return array|null
     */
    public function getErrors(): ? array
    {
        return $this->errors;
    }

    /**
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }
    
    /*
     * @param Builder $queryBuilder
     * @return string
     */
    public function getSql(Builder $queryBuilder) : string
    {
        $sql = str_replace('?', '%s', $queryBuilder->toSql());

        $handledBindings = array_map(function ($binding) {
            if (is_numeric($binding)) {
                return $binding;
            }

            if (is_bool($binding)) {
                return ($binding) ? 'true' : 'false';
            }

            return "'{$binding}'";
        }, $queryBuilder->getBindings());

        $sql = vsprintf($sql, $handledBindings);
        
        return $sql;
    }

    /**
     * Get an input element from the request.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function attr($key, $default = null)
    {
        $attrs = $this->validated();

        return isset($attrs[$key]) ? $attrs[$key] : $default;
    }
    
    public function paginate()
    {
        $query = $this->getQueryBuilder();

        $sortDir = $this->attr('sort_dir');
        $sortAttr = $this->attr('sort_attr');
        if ($sortDir && $sortAttr) {
            $query->orderBy($sortAttr, $sortDir);
        }

        $perPage = $this->attr('per_page', self::PER_PAGE);
        $page = $this->attr('page', self::PAGE);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    
}

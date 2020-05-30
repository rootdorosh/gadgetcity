<?php

namespace App\Base\Requests;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use DB;
use Closure;

abstract class BaseFilter extends FormRequest
{
    const PER_PAGE = 10;
    const PAGE = 1;

    /*
     * @var array
     */
    private $errors;

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
    public function attributes() : array
    {
        $data = __('cruds.' . Str::camel(class_basename($this)) . '.fields');

        return is_array($data) ? $data : [];
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
        $valueInt = $value;
        $valueFloat = $value;

        if ($value === null) {
            return $value;
        } elseif ((int) $valueInt  == $value) {
            return $valueInt;
        } elseif ((float) $valueFloat == $value) {
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
        if (request()->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('cruds.the_given_data_was_invalid'),
                    'errors'  => $validator->errors(),
                ], 422)
            );
        } else {
            $this->errors = $validator->errors()->toArray();
        }
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
    public static function sql(Builder $queryBuilder) : string
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

        return vsprintf($sql, $handledBindings);
    }

    /*
     * @param Builder $queryBuilder
     * @return int
     */
    public static function getCountOfQuery(Builder $queryBuilder) : int
    {
        $sql = self::sql($queryBuilder);
        $sql = explode('limit', $sql)[0];

        $count = DB::select("SELECT COUNT(*) AS `count` FROM ({$sql}) AS r")[0]->count;

        return $count;
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

    /*
     * @return Builder
     */
    abstract public function getQueryBuilder() : Builder;

    /*
     * @return bool
     */
    abstract public function authorize(): bool;

    /*
     * @param Closure $rowFormating
     * @return array
     */
    public function resolveData(Closure $rowFormating)
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
            $items[] = $rowFormating($row);
        }

        return [
            'items' => $items,
            'count' => $count,
            'from' => $offset + 1,
            'to' => $offset + min($offset + count($items), $count),
        ];
    }
}

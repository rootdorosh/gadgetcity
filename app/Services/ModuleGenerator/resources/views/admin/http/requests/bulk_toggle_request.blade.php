namespace App\Modules\{{ $moduleName }}\Admin\Http\Requests\{{ $model['name'] }};

/**
 * Class BulkToggleRequest
 * 
 * @package App\Modules\{{ $moduleName }}
 *
 */
class BulkToggleRequest extends DestroyRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('{{ strtolower($moduleName) }}.{{ strtolower($model['name']) }}.update');
    }    

    /*
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids'   => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                'exists:{{ $model['table'] }},id',
            ],
            'attribute' => [
                'required',
                'string',
                'in:is_active',
            ],
            'value' => [
                'required',
                'integer',
                'in:0,1',
            ],
        ];
    }
}

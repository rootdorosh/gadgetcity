namespace App\Modules\{{ $moduleName }}\Admin\Http\Requests\{{ $model['name'] }};

/**
 * Class BulkDestroyRequest
 * 
 * @package App\Modules\{{ $moduleName }}
 *
 */
class BulkDestroyRequest extends DestroyRequest
{
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
        ];
    }
}

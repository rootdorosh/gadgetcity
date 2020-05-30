<?php 
use Illuminate\Support\Str;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";

$rules = '';
foreach ($model['fields'] as $key => $item):
    $ruleReq = $item['required'] ? 'required':'nullable';
    $rules .= "{$tab3}'{$key}' => [\n";
    $rules .= "{$tab4}'{$ruleReq}',\n";
    $rules .= "{$tab4}'{$item['type']}',\n";
    if (!empty($item['rules']) && is_array($item['rules'])) {
        foreach ($item['rules'] as $rule) {
            $rules .= "{$tab4}'{$rule}',\n";
        }
    }
    $rules .= "{$tab3}],\n";
endforeach;

$rulesLang = '';
if (!empty($model['translatable'])):
    $rulesLang = "\n{$tab2}" . 'foreach (config(\'translatable.locales\') as $locale) {' . "\n";
    foreach ($model['translatable']['fields'] as $key => $item):
        $ruleReq = $item['required'] ? 'required':'nullable';
        $rulesLang .= "{$tab3}" . '$rules[$locale.\'.'.$key.'\'] = [' . "\n"; 
        $rulesLang .= "{$tab4}" . "'{$ruleReq}',\n";
        $rulesLang .= "{$tab4}" . "'{$item['type']}',\n";
        if (!empty($item['rules']) && is_array($item['rules'])) {
            foreach ($item['rules'] as $rule) {
                $rulesLang .= "{$tab4}'{$rule}',\n";
            }
        }
        $rulesLang .= "{$tab3}" . "];\n";
    endforeach;     
    $rulesLang .= "{$tab2}" . '}';
endif;        


?>declare( strict_types = 1 );

namespace App\Modules\{{ $moduleName }}\Admin\Http\Requests\{{ $model['name'] }};

use App\Base\Requests\BaseFormRequest;

/**
 * Class FormRequest
 * 
 * @package App\Modules\{{ $moduleName }}
 *
 */
class FormRequest extends BaseFormRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        $action = empty($this->{{ Str::snake($model['name']) }}) ? 'store' : 'update';
        
        return $this->user()->hasPermission('{{ strtolower($moduleName) }}.{{ strtolower($model['name']) }}.' . $action);
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
{!! $rules !!}{{$tab2}}];{!! $rulesLang !!}
                
        return $rules;
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('{{ $moduleName }}', '{{ $model['name'] }}');
    }
}
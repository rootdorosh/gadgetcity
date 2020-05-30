<?php 
use Illuminate\Support\Str;
?>

declare( strict_types = 1 );

namespace App\Modules\{{ $moduleName }}\Admin\Http\Requests\{{ $model['name'] }};

use App\Base\Requests\BaseSimpleRequest;

/**
 * Class CreateRequest
 */
class CreateRequest extends BaseSimpleRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('{{ strtolower($moduleName) }}.{{ strtolower($model['name']) }}.store');
    }
}

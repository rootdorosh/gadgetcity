<?php 

declare( strict_types = 1 );

namespace App\Modules\Pattern\Admin\Http\Requests\Pattern;

use App\Base\Requests\BaseDestroyRequest;

/**
 * Class DestroyRequest
 */
class DestroyRequest extends BaseDestroyRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('pattern.pattern.destroy');
    }
}

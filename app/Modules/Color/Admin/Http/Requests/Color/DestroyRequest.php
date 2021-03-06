<?php 

declare( strict_types = 1 );

namespace App\Modules\Color\Admin\Http\Requests\Color;

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
        return $this->user()->hasPermission('color.color.destroy');
    }
}

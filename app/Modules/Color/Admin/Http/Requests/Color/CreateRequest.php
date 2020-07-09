<?php 

declare( strict_types = 1 );

namespace App\Modules\Color\Admin\Http\Requests\Color;

use App\Base\Requests\BaseSimpleRequest;

/**
 * Class CreateRequest
 */
class CreateRequest extends BaseSimpleRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('color.color.store');
    }
}

<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\Product;

use App\Base\Requests\BaseSimpleRequest;

/**
 * Class EditRequest
 */
class EditRequest extends BaseSimpleRequest
{
    /*
     * @return  bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('product.product.update');
    }
}

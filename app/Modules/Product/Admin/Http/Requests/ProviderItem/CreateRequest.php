<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Admin\Http\Requests\ProviderItem;

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
        return $this->user()->hasPermission('product.provideritem.store');
    }
}

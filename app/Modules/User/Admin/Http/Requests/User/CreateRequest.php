<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\User;

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
        return allow('user.user.store');
    }    
}

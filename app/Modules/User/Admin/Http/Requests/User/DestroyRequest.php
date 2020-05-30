<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\User;

use App\Base\Requests\BaseDestroyRequest;

/**
 * Class DestroyRequest
 */
class DestroyRequest extends BaseDestroyRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('user.user.destroy');
    }    
}

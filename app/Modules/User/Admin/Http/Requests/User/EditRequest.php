<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\User;

use App\Base\Requests\BaseSimpleRequest;

/**
 * Class EditRequest
 */
class EditRequest extends BaseSimpleRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('user.user.update');
    }    
}

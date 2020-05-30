<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\Role;

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
        return allow('user.role.update');
    }    
}

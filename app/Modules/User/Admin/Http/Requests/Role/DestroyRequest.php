<?php
declare( strict_types = 1 );

namespace App\Modules\User\Admin\Http\Requests\Role;

use App\Base\Requests\BaseDestroyRequest;

/**
 * Class DestroyRequest
 * 
 * @package App\Modules\User
 *
 */
class DestroyRequest extends BaseDestroyRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('user.role.destroy');
    }    
}

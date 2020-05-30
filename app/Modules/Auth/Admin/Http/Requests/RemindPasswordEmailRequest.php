<?php
declare( strict_types = 1 );

namespace App\Modules\Auth\Admin\Http\Requests;

use App\Base\Requests\BaseFormRequest;
use App\Modules\Auth\Http\Validators\UserActive;

/**
 * Class RemindPasswordEmail
 * 
 * @package App\Modules\Auth
 */
class RemindPasswordEmail extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'email',
                new UserActive($this),
            ],
        ];
    }
    
    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('Auth', 'RemindPasswordEmail');
    }
    
}

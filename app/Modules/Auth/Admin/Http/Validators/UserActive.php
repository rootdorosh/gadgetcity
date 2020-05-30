<?php
declare( strict_types = 1 );

namespace App\Modules\Auth\Admin\Http\Validators;

use Illuminate\Contracts\Validation\Rule;
use App\Base\Requests\BaseFormRequest;
use App\Modules\User\Models\User;

/**
 * Class UserActive
 * @package App\Modules\Auth
 */
class UserActive implements Rule
{
    /*
     * @var string
     */
    private $message;

    /*
     * @var BaseFormRequest
     */
    private $request;
    
    /**
     *
     * @param BaseFormRequest $request
     */
    public function __construct(BaseFormRequest $request)
    {
        $this->request = $request;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!empty($this->request->email)) {
            $user = User::where('email', $this->request->email)->first();
            
            if (empty($user)) {
                $this->message = __('auth::login_form.invalid_credentials');
            } elseif (!$user->is_active) {
                $this->message = __('auth::login_form.account_inactive');                
            }
        }
        
        return $this->message === null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}

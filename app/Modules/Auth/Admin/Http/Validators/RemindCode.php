<?php
declare( strict_types = 1 );

namespace App\Modules\Auth\Http\Validators;

use Illuminate\Contracts\Validation\Rule;
use App\Base\Requests\BaseFormRequest;
use App\Modules\Auth\Models\PasswordReset;

/**
 * Class RemindCode
 * @package App\Modules\Auth
 */
class RemindCode implements Rule
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
        if ($this->request->code !== null  && $this->request->email !== null) {
            
            $passwordReset = PasswordReset::where('email', $this->request->email)
                ->where('token', $this->request->code)
                ->first();
            
            if (empty($passwordReset)) {
                $this->message = __('auth::validation.code_invalid');
            }
            
            return $this->message === null;
        }
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

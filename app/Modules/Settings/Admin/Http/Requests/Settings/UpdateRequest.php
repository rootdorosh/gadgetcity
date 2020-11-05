<?php
declare( strict_types = 1 );

namespace App\Modules\Settings\Admin\Http\Requests\Settings;

use App\Base\Requests\BaseFormRequest;
use App\Modules\Auth\Validators\Password;

/**
 * Class FormRequest
 *
 * @package App\Modules\User
 *
 */
class UpdateRequest extends BaseFormRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /*
     * @return array
     */
    public function attributes(): array
    {
        return $this->getAttributesLabels('settings', 'settings');
    }
}

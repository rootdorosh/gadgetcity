<?php
declare( strict_types = 1 );

namespace App\Modules\Settings\Admin\Http\Requests\Settings;

use App\Base\Requests\BaseSimpleRequest;

/**
 * Class EditRequest
 */
class IndexRequest extends BaseSimpleRequest
{
    /*
     * @return bool
     */
    public function authorize(): bool
    {
        return allow('settings.settings.index');
    }
}

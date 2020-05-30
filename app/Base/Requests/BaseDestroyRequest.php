<?php

namespace App\Base\Requests;
 
/**
 * Class BaseDestroyRequest
 * @package App\Http\Requests
 */
abstract class BaseDestroyRequest extends BaseRequest
{
    /*
     * @return bool
     */
    abstract public function authorize(): bool;
    
    /*
     * return array
     */
    public function rules(): array
    {
        return [];
    }
    
}

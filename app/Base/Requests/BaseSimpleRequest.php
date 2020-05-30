<?php

namespace App\Base\Requests;
 
/**
 * Class BaseSimpleRequest
 * @package App\Http\Requests
 */
abstract class BaseSimpleRequest extends BaseRequest
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

<?php

namespace App\Base\Requests;
 
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Exceptions\RouteParamValidationException;

/**
 * Class BaseFormRequest
 * @package App\Http\Requests
 */
abstract class BaseFormRequest extends BaseRequest
{
  
}

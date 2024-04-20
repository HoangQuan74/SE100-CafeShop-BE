<?php

namespace App\Http\Responses;

use http\Env\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use mysql_xdevapi\BaseResult;

class ApiFormResponse extends FormRequest
{
    protected Response $response;
}

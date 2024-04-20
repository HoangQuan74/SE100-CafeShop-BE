<?php

namespace App\Http\Responses;

use http\Env\Response;
use Illuminate\Foundation\Http\FormRequest;

class ApiFormResponse extends FormRequest
{
    protected Response $response;
}

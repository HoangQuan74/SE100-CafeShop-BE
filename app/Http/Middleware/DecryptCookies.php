<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class DecryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be decrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}

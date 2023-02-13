<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'webview_mobile/payment/notification',
        'webview_mobile/payment/finish',
        'webview_mobile/payment/unfinish',
        'webview_mobile/payment/error',
    ];
}

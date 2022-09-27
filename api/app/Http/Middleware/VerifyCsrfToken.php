<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '1c/order',
        '1c/feed',
        'v1/mobile/deliveries',
        'v1/mobile/payments',
        'v1/mobile/checkout',
        'v1/mobile/acquiring',
        'v1/pay',
    ];
}

<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // ...
    ];

    protected $middlewareGroups = [
        'web' => [
            // ...
        ],
        'api' => [
            // ...
        ],
    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'log.activity' => \App\Http\Middleware\LogActivity::class,
    ];
}

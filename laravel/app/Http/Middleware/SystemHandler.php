<?php

namespace App\Http\Middleware;

use App\Exceptions\SystemException;
use App\Services\System;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use View;

class SystemHandler
{
    /** @var Guard $auth */
    protected $auth;

    protected $except = [
    ];

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }

        if (!System::isInstalled())
            throw new SystemException(SystemException::SystemNotInstall);

        View::share('htmlTitle', System::getSiteName());
        View::share('siteName', System::getSiteName());

        return $next($request);
    }
}

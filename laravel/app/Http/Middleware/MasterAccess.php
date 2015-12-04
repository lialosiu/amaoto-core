<?php

namespace App\Http\Middleware;

use App\Exceptions\SecurityException;
use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class MasterAccess
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new SecurityException(SecurityException::LoginFist);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        /** @var User $user */
        $user = $this->auth->user();
        if (!$user->is_master)
            throw new SecurityException(SecurityException::NoPermission);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Exceptions\AppException;
use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AdministratorAccess
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
     * @throws AppException
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                throw new AppException(AppException::NEED_SIGN_IN);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        /** @var User $user */
        $user = $this->auth->user();
        if (!$user->is_administrator)
            throw new AppException(AppException::NO_PERMISSION);

        return $next($request);
    }
}

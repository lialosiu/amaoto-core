<?php

namespace App\Http\Middleware;

use App\Exceptions\SecurityException;
use App\UserSession;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Session;

class UserSessionHandler
{
    /** @var Guard $auth */
    protected $auth;

    protected $except = [
        '_debugbar/*',
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

        /** @var UserSession $userSession */
        $userSession = UserSession::getCurrentUserSession();

        if (!$userSession)
            $userSession = new UserSession();
        else
            $userSession->updateStatus();

        if ($userSession->is_expired) {
            Session::regenerate(true);
            $userSession = new UserSession();
        }

        $userSession->session_id = Session::getId();

        if ($userSession->is_locked) {
            if ($request->ajax() || $request->wantsJson())
                throw new SecurityException(SecurityException::SessionLocked);
            return redirect()->guest('auth/unlock'); // todo url
        }

        if ($this->auth->check())
            $userSession->user()->associate($this->auth->id());

        $userSession->last_active_time     = Carbon::now();
        $userSession->last_active_ip       = $request->ip();
        $userSession->last_active_ua       = $request->header('user-agent');
        $userSession->last_active_full_url = $request->fullUrl();

        $userSession->save();

        return $next($request);
    }
}

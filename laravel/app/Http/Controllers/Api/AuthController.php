<?php namespace App\Http\Controllers\Api;

use App\Exceptions\NotSupportedException;
use App\Exceptions\SecurityException;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Services\System;
use App\Services\Tools;
use App\Services\UserManager;
use App\User;
use App\UserInfo;
use App\UserSession;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function doSignUp(SignUpRequest $request)
    {
        $username                    = $request->get('username');
        $email                       = $request->get('email');
        $phone                       = $request->get('phone');
        $password                    = $request->get('password');
        $nickname                    = $request->has('nickname') ? $request->get('nickname') : '';
        $verificationCodeForUsername = $request->get('verification_code_for_username');
        $verificationCodeForPhone    = $request->get('verification_code_for_phone');
        $verificationCodeForEmail    = $request->get('verification_code_for_email');

        if ($username) {
            throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);
        }

        if ($email) {
            throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);
        }

        if ($phone) {
            throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);
        }

        $user = UserManager::signUp($username, $email, $phone, $password);
        $user->roles()->attach(System::getDefaultRole());
        $user->save();

        $userInfo = new UserInfo();
        $userInfo->user()->associate($user);
        $userInfo->save();

        return $this->buildResponse(trans('api.auth.sign_up.success'), $user);
    }

    /**
     * 用户登录
     *
     * @param SignInRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \App\Exceptions\SignInException
     */
    public function doSignIn(SignInRequest $request)
    {
        $loginId  = $request->get('login_id');
        $username = $request->get('username');
        $email    = $request->get('email');
        $phone    = $request->get('phone');
        $password = $request->get('password');
        $remember = $request->get('remember');

        if ($phone) {
            $user = UserManager::signInByPhone($phone, $password, $remember);
        } else if ($email) {
            $user = UserManager::signInByEmail($email, $password, $remember);
        } else if ($username) {
            $user = UserManager::signInByUsername($username, $password, $remember);
        } else {
            if (Tools::isEmail($loginId))
                $user = UserManager::signInByEmail($loginId, $password, $remember);
            else if (Tools::isPhone($loginId))
                $user = UserManager::signInByPhone($loginId, $password, $remember);
            else
                $user = UserManager::signInByUsername($loginId, $password, $remember);
        }

        return $this->buildResponse(trans('api.auth.sign_in.success'), Tools::toArray($user));
    }

    /**
     * 用户登出
     *
     * @param Guard $guard
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function doSignOut(Guard $guard)
    {
        $guard->logout();
        return $this->buildResponse(trans('api.auth.sign_out.success'));
    }

    /**
     * 解锁
     *
     * @param Request $request
     * @param Guard $guard
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws SecurityException
     */
    public function doUnlockUserSession(Request $request, Guard $guard)
    {
        $userSession = UserSession::getCurrentUserSession();
        if (!$userSession || $userSession->is_expired)
            throw new SecurityException(SecurityException::SessionExpired);

        if ($guard->check()) {
            /** @var User $user */
            $user = $guard->user();
            if (!\Hash::check($request->get('password'), $user->password))
                throw new SecurityException(SecurityException::PasswordNotMatch);
        }

        $userSession->is_locked = false;
        $userSession->save();

        return $this->buildResponse(trans('api.auth.unlock.success'));
    }

    /**
     * 获取当前用户信息
     *
     * @param Guard $guard
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws SecurityException
     */
    public function getCurrentUser(Guard $guard)
    {
        if ($guard->guest())
            throw new SecurityException(SecurityException::LoginFist);

        $user = $guard->user();

        return $this->buildResponse(trans('api.auth.get_current_user.success'), $user);
    }

}

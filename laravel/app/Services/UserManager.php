<?php namespace App\Services;

use App\Exceptions\SignInException;
use App\Exceptions\SignUpException;
use App\User;
use Auth;

class UserManager
{
    /**
     * 检查用户名是否已被使用
     *
     * @param $username
     * @return bool
     */
    public static function isUsernameExists($username)
    {
        if (User::whereUsername($username)->exists())
            return true;
        return false;
    }

    /**
     * 检查Email是否已被使用
     *
     * @param $email
     * @return bool
     */
    public static function isEmailExists($email)
    {
        if (User::whereEmail($email)->exists())
            return true;
        return false;
    }

    /**
     * 检查手机号码是否已被使用
     *
     * @param $phone
     * @return bool
     */
    public static function isPhoneExists($phone)
    {
        if (User::wherePhone($phone)->exists())
            return true;
        return false;
    }

    /**
     * 用户注册
     *
     * @param $username
     * @param $email
     * @param $phone
     * @param $password
     * @return User
     * @throws SignUpException
     */
    public static function signUp($username, $email, $phone, $password)
    {
        if (!is_null($username) && self::isUsernameExists($username))
            throw new SignUpException(SignUpException::UsernameExists, ['username' => $username]);

        if (!is_null($email) && self::isEmailExists($email))
            throw new SignUpException(SignUpException::EmailExists, ['email' => $email]);

        if (!is_null($phone) && self::isPhoneExists($phone))
            throw new SignUpException(SignUpException::PhoneExists, ['phone' => $phone]);

        $user           = new User;
        $user->username = $username;
        $user->email    = Tools::isEmail($email) ? $email : null;
        $user->phone    = Tools::isPhone($phone) ? $phone : null;
        $user->password = bcrypt($password);

        $user->save();

        return $user;
    }

    /**
     * 以用户名登录
     *
     * @param $username
     * @param $password
     * @param bool $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws SignInException
     */
    public static function signInByUsername($username, $password, $remember = false)
    {
        if (!UserManager::isUsernameExists($username))
            throw new SignInException(SignInException::UsernameNotExists);

        if (Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
            return Auth::user();
        }

        throw new SignInException(SignInException::PasswordNotMatch);
    }


    /**
     * 以 Email 登录
     *
     * @param $email
     * @param $password
     * @param bool $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws SignInException
     */
    public static function signInByEmail($email, $password, $remember = false)
    {
        if (!UserManager::isEmailExists($email))
            throw new SignInException(SignInException::EmailNotExists);

        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            return Auth::user();
        }

        throw new SignInException(SignInException::PasswordNotMatch);
    }

    /**
     * 以 手机号码 登录
     *
     * @param $phone
     * @param $password
     * @param bool $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws SignInException
     */
    public static function signInByPhone($phone, $password, $remember = false)
    {
        if (!UserManager::isPhoneExists($phone))
            throw new SignInException(SignInException::PhoneNotExists);

        if (Auth::attempt(['phone' => $phone, 'password' => $password], $remember)) {
            return Auth::user();
        }

        throw new SignInException(SignInException::PasswordNotMatch);
    }
}

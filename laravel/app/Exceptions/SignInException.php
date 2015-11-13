<?php namespace App\Exceptions;

use Exception;

class SignInException extends Exception
{
    const UsernameNotExists = 0x01;
    const EmailNotExists = 0x02;
    const PhoneNotExists = 0x03;
    const PasswordNotMatch = 0x04;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::UsernameNotExists:
                $message = trans('exception.sign_in.username.not_exists', ['username' => array_get($data, 'username')]);
                break;
            case self::EmailNotExists:
                $message = trans('exception.sign_in.email.not_exists', ['email' => array_get($data, 'email')]);
                break;
            case self::PhoneNotExists:
                $message = trans('exception.sign_in.phone.not_exists', ['phone' => array_get($data, 'phone')]);
                break;
            case self::PasswordNotMatch:
                $message = trans('exception.sign_in.password.not_match');
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
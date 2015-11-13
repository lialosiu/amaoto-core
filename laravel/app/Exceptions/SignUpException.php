<?php namespace App\Exceptions;

use Exception;

class SignUpException extends Exception
{
    const UsernameExists = 0x01;
    const EmailExists    = 0x02;
    const PhoneExists    = 0x03;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::UsernameExists:
                $message = trans('exception.sign_up.username.exists', ['username' => array_get($data, 'username')]);
                break;
            case self::EmailExists:
                $message = trans('exception.sign_up.email.exists', ['email' => array_get($data, 'email')]);
                break;
            case self::PhoneExists:
                $message = trans('exception.sign_up.phone.exists', ['phone' => array_get($data, 'phone')]);
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
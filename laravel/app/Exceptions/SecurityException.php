<?php namespace App\Exceptions;

use Exception;

class SecurityException extends Exception
{
    const LoginFist                = 0x01;
    const NoPermission             = 0x02;
    const Wait60Seconds            = 0x03;
    const VerificationCodeNotMatch = 0x04;
    const PasswordNotMatch         = 0x05;
    const SessionExpired           = 0x06;
    const SessionLocked            = 0x07;

    public function __construct($code = 0, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::LoginFist:
                $message = trans('exception.security.login_first');
                break;
            case self::NoPermission:
                $message = trans('exception.security.no_permission');
                break;
            case self::Wait60Seconds:
                $message = trans('exception.security.wait_60_seconds');
                break;
            case self::VerificationCodeNotMatch:
                $message = trans('exception.security.verification_code_not_match');
                break;
            case self::PasswordNotMatch:
                $message = trans('exception.security.password_not_match');
                break;
            case self::SessionExpired:
                $message = trans('exception.security.session_expired');
                break;
            case self::SessionLocked:
                $message = trans('exception.security.session_locked');
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
<?php namespace App\Exceptions;

use Exception;

class DataException extends Exception
{
    const UserIsAlreadyHasOaUser = 0x01;
    const UserDoNotHasOaUser     = 0x02;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::UserIsAlreadyHasOaUser:
                $message = trans('exception.data.user_is_already_has_oa_user', []);
                break;
            case self::UserDoNotHasOaUser:
                $message = trans('exception.data.user_do_not_has_oa_user', []);
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
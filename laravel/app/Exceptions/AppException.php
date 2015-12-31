<?php namespace App\Exceptions;

use Exception;

class AppException extends Exception
{
    const NEED_SIGN_IN  = 0x10001;
    const NO_PERMISSION = 0x10002;

    const USERNAME_NOT_EXISTS = 0x11001;
    const PASSWORD_NOT_MATCH  = 0x11002;

    const USER_NOT_FOUND  = 0x40001;
    const MUSIC_NOT_FOUND = 0x40002;
    const ALBUM_NOT_FOUND = 0x40003;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::USER_NOT_FOUND:
                $message = trans('exception.app.user_not_found');
                break;
            case self::MUSIC_NOT_FOUND:
                $message = trans('exception.app.music_not_found');
                break;
            case self::ALBUM_NOT_FOUND:
                $message = trans('exception.app.album_not_found');
                break;
            default:
                $message = trans('str.unknown_exception') . '[' . $code . ']';
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
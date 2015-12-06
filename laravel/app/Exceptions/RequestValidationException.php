<?php namespace App\Exceptions;

use Exception;

class RequestValidationException extends Exception
{
    const ValidationFail = 0x01;
    const FileIsNotImage = 0x02;
    const FileIsNotMusic = 0x03;

    private $errors;

    public function __construct($code, array $errors = [], Exception $previous = null)
    {
        $this->errors = $errors;
        switch ($code) {
            case self::ValidationFail:
                $message = trans('exception.request_validation.validation_fail', []);
                break;
            case self::FileIsNotImage:
                $message = trans('exception.request_validation.file_is_not_image', []);
                break;
            case self::FileIsNotMusic:
                $message = trans('exception.request_validation.file_is_not_music', []);
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
<?php namespace App\Exceptions;

use Exception;

class FileUploadException extends Exception
{
    const UploadFail          = 0x01;
    const UploadFailWithError = 0x02;
    const RebuildChunkError   = 0x03;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::UploadFail:
                $message = trans('exception.file_upload.fail', []);
                break;
            case self::UploadFailWithError:
                $message = $data['error'];
                break;
            case self::RebuildChunkError:
                $message = trans('exception.file_upload.rebuild_chunk');
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
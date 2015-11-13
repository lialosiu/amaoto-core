<?php namespace App\Exceptions;

use Exception;

class NotSupportedException extends Exception
{
    const FeatureOnTheWay = 0x00;
    const SocialiteSource = 0x01;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::FeatureOnTheWay:
                $message = trans('exception.not_supported.feature_on_the_way', []);
                break;
            case self::SocialiteSource:
                $message = trans('exception.not_supported.socialite_source', []);
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
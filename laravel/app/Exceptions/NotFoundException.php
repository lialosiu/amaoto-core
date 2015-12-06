<?php namespace App\Exceptions;

use App\UserGroup;
use Exception;

class NotFoundException extends Exception
{
    const UserNotFound         = 0x00;
    const FileNotFound         = 0x01;
    const ImageNotFound        = 0x02;
    const AvatarNotFound       = 0x03;
    const TweetNotFound        = 0x04;
    const PostNotFound         = 0x05;
    const TopicNotFound        = 0x06;
    const TagNotFound          = 0x07;
    const UserInfoNotFound     = 0x08;
    const AlertNotFound        = 0x09;
    const UserGroupNotFound    = 0x0a;
    const PostCommentNotFound  = 0x0b;
    const TweetCommentNotFound = 0x0c;
    const DepartmentNotFound   = 0x0d;
    const MusicNotFound        = 0x0e;

    public function __construct($code, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::UserNotFound:
                $message = trans('exception.not_found.user', []);
                break;
            case self::UserInfoNotFound:
                $message = trans('exception.not_found.user_info', []);
                break;
            case self::FileNotFound:
                $message = trans('exception.not_found.file', []);
                break;
            case self::ImageNotFound:
                $message = trans('exception.not_found.image', []);
                break;
            case self::AvatarNotFound:
                $message = trans('exception.not_found.avatar', []);
                break;
            case self::TweetNotFound:
                $message = trans('exception.not_found.tweet', []);
                break;
            case self::PostNotFound:
                $message = trans('exception.not_found.post', []);
                break;
            case self::TopicNotFound:
                $message = trans('exception.not_found.topic', []);
                break;
            case self::TagNotFound:
                $message = trans('exception.not_found.tag', []);
                break;
            case self::AlertNotFound:
                $message = trans('exception.not_found.alert', []);
                break;
            case self::UserGroupNotFound:
                $message = trans('exception.not_found.user_group', []);
                break;
            case self::PostCommentNotFound:
                $message = trans('exception.not_found.post_comment', []);
                break;
            case self::TweetCommentNotFound:
                $message = trans('exception.not_found.tweet_comment', []);
                break;
            case self::DepartmentNotFound:
                $message = trans('exception.not_found.department', []);
                break;
            case self::MusicNotFound:
                $message = trans('exception.not_found.music_not_found', []);
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}
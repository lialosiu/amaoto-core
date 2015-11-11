<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\UserInfo
 *
 * @property integer $id
 * @property integer $user_id 用户外键
 * @property integer $avatar_id 头像外键
 * @property string $sex 性别
 * @property string $birthday 生日
 * @property string $qq QQ
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereAvatarId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereSex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereBirthday($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereQq($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserInfo whereUpdatedAt($value)
 */
class UserInfo extends Model
{
    protected $table = 'user_infos';

    use SoftDeletes;

}

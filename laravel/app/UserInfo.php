<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\UserInfo
 *
 * @property integer $id
 * @property string $nick_name 用户昵称
 * @property string $sex 性别
 * @property string $birthday 生日
 * @property string $qq QQ
 * @property string $profile 个性签名
 * @property integer $user_id 用户外键
 * @property integer $avatar_id 头像外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 */
class UserInfo extends Eloquent
{
    protected $table = 'user_infos';

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

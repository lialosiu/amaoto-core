<?php

namespace App;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\UserSession
 *
 * @property integer $id
 * @property string $session_id SessionID
 * @property integer $user_id 用户外键
 * @property string $login_time 登录时间
 * @property string $login_ip 登录IP
 * @property string $login_ua 登录UA
 * @property string $last_active_time 最后活动时间
 * @property string $last_active_ip 最后活动IP
 * @property string $last_active_ua 最后活动UA
 * @property string $last_active_full_url 最后活动URL
 * @property boolean $is_locked 是否已锁定
 * @property boolean $is_expired 是否已过期
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @property-read mixed $username
 */
class UserSession extends Eloquent
{
    protected $table   = 'user_sessions';
    protected $appends = ['username'];

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUsernameAttribute()
    {
        if ($this->user)
            return $this->user->username;
        return '[' . trans('str.unknown_user') . ']';
    }

    public function updateStatus()
    {
        if (config('session.lock_time') && (new Carbon($this->last_active_time))->addMinute(config('session.lock_time')) <= Carbon::now()) {
            $this->is_locked = true;
        }

        if (config('session.lifetime') && (new Carbon($this->last_active_time))->addMinute(config('session.lifetime')) <= Carbon::now()) {
            $this->is_expired = true;
        }
    }

    public function updateSession($ip, $ua, $url)
    {
        if ((new Carbon($this->last_active_time))->addMinute(config('session.lifetime')) <= Carbon::now()) {
            $this->is_locked = true;
        } else {
            $this->last_active_time     = Carbon::now();
            $this->last_active_ip       = $ip;
            $this->last_active_ua       = $ua;
            $this->last_active_full_url = $url;
        }
    }

    /**
     * @return UserSession|mixed|static
     */
    public static function getCurrentUserSession()
    {
        return self::whereSessionId(\Session::getId())->first();
    }
}

<?php

namespace App;

use App\Services\System;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * App\User
 *
 * @property integer $id
 * @property string $username 用户名
 * @property string $email Email
 * @property string $phone 电话
 * @property string $password 密码
 * @property boolean $is_baned 是否禁用
 * @property string $remember_token
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\UserInfo $userInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read mixed $is_master
 * @property-read mixed $is_administrator
 * @property-read mixed $is_editor
 * @property-read mixed $is_default
 */
class User extends Eloquent implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    protected $table   = 'users';
    protected $hidden  = ['password', 'remember_token'];
    protected $appends = ['is_master', 'is_administrator', 'is_editor', 'is_default'];

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_x_role', 'user_id', 'role_id');
    }

    public function getIsMasterAttribute()
    {
        $masterRoleId = System::getMasterRole()->id;
        foreach ($this->roles as $role)
            if ($role->id == $masterRoleId)
                return true;
        return false;
    }

    public function getIsAdministratorAttribute()
    {
        $administratorRoleId = System::getAdministratorRole()->id;
        foreach ($this->roles as $role)
            if ($role->id == $administratorRoleId)
                return true;
        return false;
    }

    public function getIsEditorAttribute()
    {
        $editorRoleId = System::getEditorRole()->id;
        foreach ($this->roles as $role)
            if ($role->id == $editorRoleId)
                return true;
        return false;
    }

    public function getIsDefaultAttribute()
    {
        $defaultRoleId = System::getDefaultRole()->id;
        foreach ($this->roles as $role)
            if ($role->id == $defaultRoleId)
                return true;
        return false;
    }
}
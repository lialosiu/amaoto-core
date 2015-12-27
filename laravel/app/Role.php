<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Role
 *
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 */
class Role extends Eloquent
{
    protected $table = 'roles';

    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_x_role', 'role_id', 'user_id');
    }

}

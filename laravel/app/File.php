<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\File
 *
 * @property integer $id
 * @property string $name 文件名
 * @property string $ext 后缀名
 * @property string $mime MIME
 * @property integer $user_id 上传用户外键
 * @property integer $base_file_id 基础文件外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\BasicFile $baseFile
 * @property-read \App\User $user
 */
class File extends Eloquent
{
    protected $table = 'files';

    use SoftDeletes;

    public function baseFile()
    {
        return $this->belongsTo(BasicFile::class, 'base_file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

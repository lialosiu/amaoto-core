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
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read BasicFile $baseFile
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereExt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereMime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereBaseFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUpdatedAt($value)
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

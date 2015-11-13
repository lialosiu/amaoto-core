<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\BasicFile
 *
 * @property integer $id
 * @property string $md5 MD5
 * @property string $sha1 SHA1
 * @property integer $size 文件大小
 * @property string $disk 文件储存磁盘
 * @property string $path 文件储存路径
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereSha1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereDisk($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasicFile whereUpdatedAt($value)
 */
class BasicFile extends Model
{
    protected $table = 'basic_files';

    use SoftDeletes;
}

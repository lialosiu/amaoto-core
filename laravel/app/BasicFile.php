<?php

namespace App;

use App\Exceptions\NotSupportedException;
use Eloquent;
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
 */
class BasicFile extends Eloquent
{
    protected $table = 'basic_files';

    use SoftDeletes;

    public function getLocalCachePath()
    {
        switch ($this->disk) {
            case \Storage::getDefaultDriver():
                $path = config('filesystems.disks.local.root') . '/' . $this->path;
                break;
            default:
                throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);
                break;
        }
        return $path;
    }
}

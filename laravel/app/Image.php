<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Image
 *
 * @property integer $id
 * @property integer $width 宽度
 * @property integer $height 高度
 * @property integer $file_id 原始文件外键
 * @property integer $thumbnail_file_id 缩略图文件外键
 * @property integer $high_resolution_file_id 高分辨率文件外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\File $file
 * @property-read \App\File $thumbnailFile
 * @property-read \App\File $highResolutionFile
 * @property-read mixed $show_url
 * @property-read mixed $show_thumbnail_url
 * @property-read mixed $show_high_resolution_url
 */
class Image extends Eloquent
{
    protected $table   = 'images';
    protected $appends = ['show_url', 'show_thumbnail_url', 'show_high_resolution_url'];

    use SoftDeletes;

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function thumbnailFile()
    {
        return $this->belongsTo(File::class, 'thumbnail_file_id');
    }

    public function highResolutionFile()
    {
        return $this->belongsTo(File::class, 'high_resolution_file_id');
    }

    public function getShowUrlAttribute()
    {
        return action('Api\ImageController@getImageBinToShowById', ['id' => $this->id]);
    }

    public function getShowThumbnailUrlAttribute()
    {
        return action('Api\ImageController@getImageBinToShowById', ['id' => $this->id, 'size' => 'thumbnail']);
    }

    public function getShowHighResolutionUrlAttribute()
    {
        return action('Api\ImageController@getImageBinToShowById', ['id' => $this->id, 'size' => 'high-resolution']);
    }
}

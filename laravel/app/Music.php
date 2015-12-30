<?php

namespace App;

use Eloquent;

/**
 * App\Music
 *
 * @property integer $id
 * @property string $title 歌名
 * @property string $artist 艺术家
 * @property string $year 年份
 * @property integer $track 音轨
 * @property string $genre 流派
 * @property float $playtime 播放时长
 * @property float $bitrate 码流
 * @property string $album_title 专辑标题
 * @property string $album_artist 专辑艺术家
 * @property string $tags 标签数据
 * @property integer $file_id 文件外键
 * @property integer $cover_image_id 封面图片外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\File $file
 * @property-read \App\Image $coverImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Album[] $albums
 * @property-read mixed $show_url
 * @property-read mixed $show_thumbnail_cover_url
 * @property-read mixed $bitrate4_display
 */
class Music extends Eloquent
{
    protected $table   = 'musics';
    protected $appends = ['show_url', 'show_thumbnail_cover_url', 'bitrate_4_display'];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function coverImage()
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_x_music', 'music_id', 'album_id');
    }

    public function getShowUrlAttribute()
    {
        return action('Api\MusicController@getMusicBinToShowById', ['id' => $this->id]);
    }

    public function getShowThumbnailCoverUrlAttribute()
    {
        if (!$this->coverImage)
            return '';
        return $this->coverImage->show_thumbnail_url;
    }

    public function getBitrate4DisplayAttribute()
    {
        return sprintf('%dkbps', round($this->bitrate / 1000));
    }
}

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
 * @property string $tags 标签数据
 * @property integer $file_id 文件外键
 * @property integer $cover_image_id 封面图片外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read File $file
 * @property-read Image $coverImage
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereArtist($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereTrack($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereGenre($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music wherePlaytime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereBitrate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereTags($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereCoverImageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Music whereUpdatedAt($value)
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

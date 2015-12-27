<?php

namespace App;

use Eloquent;

/**
 * App\Album
 *
 * @property integer $id
 * @property string $title 歌名
 * @property string $artist 艺术家
 * @property string $year 年份
 * @property string $genre 流派
 * @property integer $cover_image_id 封面图片外键
 * @property integer $user_id 创建用户外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Music[] $musics
 * @property-read \App\User $user
 * @property-read \App\Image $coverImage
 * @property-read mixed $show_url
 * @property-read mixed $show_thumbnail_cover_url
 * @property-read mixed $music_count
 */
class Album extends Eloquent
{
    protected $table = 'albums';
    protected $appends = ['show_url', 'show_thumbnail_cover_url', 'music_count'];

    public function musics()
    {
        return $this->belongsToMany(Music::class, 'album_x_music', 'album_id', 'music_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function getMusicCountAttribute()
    {
        return $this->musics->count();
    }
}

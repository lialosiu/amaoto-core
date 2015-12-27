<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Avatar
 *
 * @property integer $id
 * @property integer $image_id 图像外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Image $image
 */
class Avatar extends Eloquent
{
    protected $table = 'avatars';

    use SoftDeletes;

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}

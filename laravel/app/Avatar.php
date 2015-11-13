<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Avatar
 *
 * @property integer $id
 * @property integer $image_id 图像外键
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Image $image
 * @method static \Illuminate\Database\Query\Builder|\App\Avatar whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Avatar whereImageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Avatar whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Avatar whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Avatar whereUpdatedAt($value)
 */
class Avatar extends Model
{
    protected $table = 'avatars';

    use SoftDeletes;

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Option
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Option whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Option whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Option whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Option whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Option whereUpdatedAt($value)
 */
class Option extends Model
{
    protected $table    = 'options';
    protected $fillable = ['key'];

    /**
     * @param $key
     * @param string $value
     * @return string
     */
    public static function setValueByKey($key, $value)
    {
        $option = self::firstOrNew([
            'key' => $key,
        ]);

        $option->value = $value;
        $option->save();

        return $option->value;
    }

    /**
     * @param $key
     * @return string
     */
    public static function getValueByKey($key)
    {
        $option = self::firstOrNew([
            'key' => $key,
        ]);

        return $option->value;
    }
}

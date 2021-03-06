<?php

namespace App;

use Eloquent;

/**
 * App\Option
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Option extends Eloquent
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

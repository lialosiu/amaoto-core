<?php namespace App\Services;

use Illuminate\Support\Debug\Dumper;
use Request;
use Validator;

class Tools
{
    /**
     * 验证是否为合法的 Email 地址
     *
     * @param $str
     * @return bool
     */
    public static function isEmail($str)
    {
        return Validator::make(['email' => $str], ['email' => 'required|email'])->passes();
    }

    /**
     * 验证是否为合法的手机号码
     *
     * @param $str
     * @return bool
     */
    public static function isPhone($str)
    {
        return Validator::make(['phone' => $str], ['phone' => ['required', 'regex:/1\d{10}/']])->passes();
    }

    /**
     * 尝试转换为数组
     *
     * @param $obj
     * @return mixed
     */
    public static function toArray($obj)
    {
        if (method_exists($obj, 'toArray'))
            return $obj->toArray();
        return $obj;
    }

    /**
     * 输出对象 Debug 信息
     *
     * @param $obj
     * @return string
     */
    public static function dump($obj)
    {
        (new Dumper)->dump($obj);
        return '';
    }

}
<?php namespace App\Http\Requests;

class SignUpRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'username'                       => '用户名',
            'email'                          => 'Email',
            'phone'                          => '手机号码',
            'password'                       => '密码',
            'nickname'                       => '昵称',
            'verification_code_for_username' => '用于用户名注册的验证码',
            'verification_code_for_email'    => '用于Email注册的验证码',
            'verification_code_for_phone'    => '用于手机号码注册的验证码',
        ];
    }

    public function rules()
    {
        return [
            'username'                       => 'required_without_all:email,phone|alpha_dash|min:2|max:20|unique:users,username',
            'email'                          => 'required_without_all:username,phone|email|max:255|unique:users,email',
            'phone'                          => ['required_without_all:username,email', 'regex:/1\d{10}/', 'unique:users,phone'],
            'password'                       => 'required|confirmed|min:6|max:255',
            'nickname'                       => 'min:1|max:20',
            'verification_code_for_username' => 'required_with:username',
            'verification_code_for_email'    => 'required_with:email',
            'verification_code_for_phone'    => 'required_with:phone',
        ];
    }


}

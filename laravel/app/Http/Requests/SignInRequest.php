<?php namespace App\Http\Requests;

class SignInRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'login_id' => '登录名',
            'username' => '用户名',
            'email'    => 'Email',
            'phone'    => '手机号码',
            'password' => '密码',
        ];
    }

    public function rules()
    {
        return [
            'login_id' => 'required_without_all:username,email,phone',
            'username' => 'required_without_all:login_id,email,phone|alpha_dash|min:2|max:20|exists:users,username',
            'email'    => 'required_without_all:login_id,username,phone|email|max:255|exists:users,email',
            'phone'    => ['required_without_all:login_id,username,email', 'regex:/1\d{10}/', 'exists:users,phone'],
            'password' => 'required|min:6|max:255',
        ];
    }
}

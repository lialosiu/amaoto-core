<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Services\Tools;
use App\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function getUsersWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $users = User::orderBy('id', 'desc')->paginate($perPage);

        return $this->buildResponse(trans('api.user.paginate.success'), Tools::toArray($users));
    }
}

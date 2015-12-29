<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Services\System;
use Illuminate\Http\Request;

class SystemController extends BaseController
{
    public function getInfo()
    {
        return $this->buildResponse(trans('api.system.get-info.success'), [
            'site_name'    => System::getSiteName(),
            'powered_name' => System::getPoweredName(),
            'version'      => System::getVersion(),
        ]);
    }

    public function doSaveSetting(Request $request)
    {
        if ($request->has('site_name'))
            System::setSiteName($request->get('site_name'));
        if ($request->has('version'))
            System::setVersion($request->get('version'));
        if ($request->has('powered_name'))
            System::setPoweredName($request->get('powered_name'));

        return $this->buildResponse(trans('api.system.save-setting.success'));
    }
}

<?php namespace App\Http\Controllers\Api;

use App\Album;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Music;
use App\Services\System;
use App\Services\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SystemController extends BaseController
{
    public function getInfo()
    {
        return $this->buildResponse(trans('api.system.get-info.success'), [
            'site_name'    => System::getSiteName(),
            'powered_name' => System::getPoweredName(),
            'version'      => System::getVersion(),
            'music_count'  => Music::count(),
            'album_count'  => Album::count(),
        ]);
    }

    public function getAmaotoFlow()
    {
        $flow = new Collection();
        Music::orderByRaw('RAND()')->take(10)->get()->each(function ($item) use (&$flow) {
            /** @var Music $item */
            $flow->push(['type' => 'music', 'data' => $item->toArray()]);
        });
        Album::orderByRaw('RAND()')->take(10)->get()->each(function ($item) use (&$flow) {
            /** @var Album $item */
            $flow->push(['type' => 'album', 'data' => $item->toArray()]);
        });
        $result = $flow->shuffle();

        return $this->buildResponse(trans('api.system.get-amaoto-flow.success'), Tools::toArray($result));
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

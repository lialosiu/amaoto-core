<?php namespace App\Http\Controllers\Api;

use App\Album;
use App\Exceptions\NotFoundException;
use App\Exceptions\SecurityException;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Services\Tools;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class AlbumController extends BaseController
{
    public function doCreateAlbum(Guard $guard, Request $request)
    {
        if ($guard->guest())
            throw new SecurityException(SecurityException::LoginFist);

        $musics       = $request->get('musics');
        $coverImageId = $request->get('cover_image_id');

        $title  = $request->get('title');
        $artist = $request->get('artist');
        $year   = $request->get('year');
        $genre  = $request->get('genre');

        $album         = new Album();
        $album->title  = $title;
        $album->artist = $artist;
        $album->year   = $year;
        $album->genre  = $genre;
        $album->coverImage()->associate($coverImageId);
        $album->user()->associate($guard->id());
        $album->save();

        foreach ($musics as $musicId) {
            $album->musics()->attach($musicId);
        }

        return $this->buildResponse(trans('api.album.create.success'), Tools::toArray($album));
    }

    public function getAlbumById($id = 0)
    {
        /** @var Album $album */
        $album = Album::where('id', $id)->first();

        if (!$album)
            throw new NotFoundException(NotFoundException::ALBUM_NOT_FOUND);

        return $this->buildResponse(trans('api.album.get.success'), Tools::toArray($album));
    }

    public function getAlbumsWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $albums = Album::orderBy('id', 'desc')->paginate($perPage);

        return $this->buildResponse(trans('api.album.paginate.success'), Tools::toArray($albums));
    }
}

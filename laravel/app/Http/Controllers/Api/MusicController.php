<?php namespace App\Http\Controllers\Api;

use App\Album;
use App\Exceptions\AppException;
use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Music as MusicModel;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MusicController extends BaseController
{
    public function doUploadMusic(Guard $guard, Request $request)
    {
        if ($guard->guest())
            throw new AppException(AppException::NEED_SIGN_IN);

        $uploadedFile = $request->file('file');

        if (!$uploadedFile) {
            throw new FileUploadException(FileUploadException::UploadFail);
        }

        if (!$uploadedFile->isValid()) {
            throw new FileUploadException(FileUploadException::UploadFailWithError, [
                'error' => $uploadedFile->getErrorMessage(),
            ]);
        }

//        $chunkSize   = $request->get('_chunkSize');
//        $chunkNumber = $request->get('_chunkNumber');
        $totalSize = $request->get('_totalSize');

        $uniName = $request->has('uniName')
            ? $guard->id() . '-' . md5($request->get('uniName'))
            : $guard->id() . '-' . md5($uploadedFile->getClientOriginalName() . '-' . $uploadedFile->getClientMimeType());

        $filePath = FileManager::rebuildChunkFile($uploadedFile->getRealPath(), $uniName, $totalSize);
        if ($filePath == false)
            return $this->buildResponse(trans('api.music.upload.continue'));

        $fileName = $request->get('filename');
        if (!$fileName)
            $fileName = $uploadedFile->getClientOriginalName();

        /** @var User $user */
        $user = $guard->user();

        $music = FileManager::UploadMusic($filePath, $fileName, $user);

        if ($request->has('albumId')) {
            $id = $request->get('albumId');
            /** @var Album $album */
            $album = Album::where('id', $id)->first();
            if (!$album)
                throw new AppException(AppException::ALBUM_NOT_FOUND);
            $album->musics()->attach($music);
        }
        return $this->buildResponse(trans('api.music.upload.success'), $music);
    }

    public function doEditMusicById($id, Request $request)
    {
        /** @var MusicModel $music */
        $music = MusicModel::where('id', $id)->first();
        if (!$music)
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        if ($request->has('title'))
            $music->title = $request->get('title');
        if ($request->has('artist'))
            $music->artist = $request->get('artist');
        if ($request->has('year'))
            $music->year = $request->get('year');
        if ($request->has('genre'))
            $music->genre = $request->get('genre');
        if ($request->has('cover_image_id'))
            $music->coverImage()->associate($request->get('cover_image_id'));

        $music->save();

        return $this->buildResponse(trans('api.music.edit.success'), $music);
    }

    public function doDeleteMusicById($id)
    {
        /** @var MusicModel $music */
        $music = MusicModel::where('id', $id)->first();
        if (!$music)
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        $music->delete();

        return $this->buildResponse(trans('api.music.delete.success'), $music);
    }

    public function getMusicBinToDownloadById($id)
    {
        /** @var MusicModel $music */
        $music = MusicModel::where('id', $id)->first();
        if (!$music || !$music->file || !$music->file->baseFile)
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        return \Response::download($music->file->baseFile->getLocalCachePath(), $music->title . '.' . $music->file->ext);
    }

    public function getMusicBinToShowById($id)
    {
        /** @var MusicModel $music */
        $music = MusicModel::where('id', $id)->first();

        if (!$music || !$music->file || !$music->file->baseFile)
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        return \Response::download($music->file->baseFile->getLocalCachePath(), $music->title . '.' . $music->file->ext, [], 'inline');
    }

    public function getMusicsByIds(Request $request)
    {
        $ids = $request->get('ids');

        if (!is_array($ids))
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        $musics = MusicModel::whereIn('id', $ids)->get();

        return $this->buildResponse(trans('api.musics.get.success'), $musics);
    }

    public function getMusicById($id = 0)
    {
        /** @var MusicModel $music */
        $music = MusicModel::where('id', $id)->first();

        if (!$music)
            throw new NotFoundException(NotFoundException::MUSIC_NOT_FOUND);

        return $this->buildResponse(trans('api.music.get.success'), $music);
    }

    public function getMusicsWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $musics = MusicModel::orderBy('id', 'desc')->paginate($perPage);

        return $this->buildResponse(trans('api.music.paginate.success'), Tools::toArray($musics));
    }

    public function getUploadedFileSize(Guard $guard, Request $request)
    {
        if ($guard->guest())
            throw new AppException(AppException::NEED_SIGN_IN);

        $uniName = $guard->id() . '-' . md5($request->get('uniName'));

        $size = FileManager::getMergingFileSize($uniName);

        return response()->json($size);
    }
}

<?php namespace App\Http\Controllers\Api;

use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\Exceptions\SecurityException;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Music as MusicModel;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class MusicController extends BaseController
{
    public function doUploadMusic(Guard $guard, Request $request)
    {
        if ($guard->guest())
            throw new SecurityException(SecurityException::LoginFist);

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
        return $this->buildResponse(trans('api.music.upload.success'), $music);
    }

    public function getMusicBinToDownloadById($id)
    {
        /** @var MusicModel $music */
        $music = MusicModel::whereId($id)->first();
        if (!$music || !$music->file || !$music->file->baseFile)
            throw new NotFoundException(NotFoundException::MusicNotFound);

        return \Response::download($music->file->baseFile->getLocalCachePath(), $music->file->name);
    }

    public function getMusicBinToShowById($id)
    {
        /** @var MusicModel $music */
        $music = MusicModel::whereId($id)->first();

        if (!$music || !$music->file || !$music->file->baseFile)
            throw new NotFoundException(NotFoundException::MusicNotFound);

        return \Response::download($music->file->baseFile->getLocalCachePath(), $music->file->name, [], 'inline');
    }

    public function getMusicById($id = 0)
    {
        /** @var MusicModel $music */
        $music = MusicModel::whereId($id)->first();

        if (!$music)
            throw new NotFoundException(NotFoundException::MusicNotFound);

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
            throw new SecurityException(SecurityException::LoginFist);

        $uniName = $guard->id() . '-' . md5($request->get('uniName'));

        $size = FileManager::getMergingFileSize($uniName);

        return response()->json($size);
    }
}

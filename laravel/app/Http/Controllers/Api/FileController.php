<?php namespace App\Http\Controllers\Api;

use App\Exceptions\AppException;
use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\File as FileModel;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    public function doUploadFile(Guard $guard, Request $request)
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
            return $this->buildResponse(trans('api.file.upload.continue'));

        $fileName = $request->get('filename');
        if (!$fileName)
            $fileName = $uploadedFile->getClientOriginalName();

        /** @var User $user */
        $user = $guard->user();

        $file = FileManager::UploadFileByTypeCheck($filePath, $fileName, $user);
        return $this->buildResponse(trans('api.file.upload.success'), $file);
    }

    public function getFileBinToDownloadById($id)
    {
        /** @var FileModel $file */
        $file = FileModel::where('id', $id)->first();
        if (!$file || !$file->baseFile)
            throw new NotFoundException(NotFoundException::FileNotFound);

        return \Response::download($file->baseFile->getLocalCachePath(), $file->name);
    }

    public function getFileBinToShowById($id)
    {
        /** @var FileModel $file */
        $file = FileModel::where('id', $id)->first();
        if (!$file || !$file->baseFile)
            throw new NotFoundException(NotFoundException::FileNotFound);

        return \Response::download($file->baseFile->getLocalCachePath(), [], 'inline');
    }

    public function getFileById($id = 0)
    {
        /** @var FileModel $file */
        $file = FileModel::where('id', $id)->first();

        if (!$file)
            throw new NotFoundException(NotFoundException::FileNotFound);

        return $this->buildResponse(trans('api.file.get.success'), $file);
    }

    public function getFilesWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $files = FileModel::orderBy('id', 'desc')->paginate($perPage);

        return $this->buildResponse(trans('api.file.paginate.success'), Tools::toArray($files));
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

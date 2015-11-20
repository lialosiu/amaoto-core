<?php namespace App\Http\Controllers\Api;

use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSupportedException;
use App\Exceptions\SecurityException;
use App\File as FileModel;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    public function doUploadFile(Guard $guard, Request $request)
    {
        if ($guard->guest())
            throw new SecurityException(SecurityException::LoginFist);

        $uploadedFile = $request->file('file');

        if (!$request->hasFile('file')) {
            throw new FileUploadException(FileUploadException::UploadFail);
        }

        if (!$uploadedFile->isValid()) {
            throw new FileUploadException(FileUploadException::UploadFailWithError, ['error' => $uploadedFile->getErrorMessage()]);
        }

        $chunk   = $request->has('chunk') ? $request->get('chunk') : 0;
        $chunks  = $request->has('chunks') ? $request->get('chunks') : 1;
        $uniName = $request->has('name') ? $guard->id() . '-' . $request->get('name') : $guard->id() . '-' . $uploadedFile->getClientSize() . '-' . time();

        $filePath = FileManager::rebuildChunkFile($uploadedFile->getRealPath(), $uniName, $chunk, $chunks);
        if ($filePath == false)
            return $this->buildResponse(trans('api.file.upload.continue'));

        $fileName = $request->get('filename');
        if (!$fileName)
            $fileName = $uploadedFile->getClientOriginalName();

        /** @var User $user */
        $user = $guard->user();

        $file = FileManager::UploadFile($filePath, $fileName, $user);
        return $this->buildResponse(trans('api.file.upload.success'), $file);
    }

    public function getFileBinToDownloadById($id)
    {
        /** @var FileModel $file */
        $file = FileModel::whereId($id)->first();
        if (!$file || !$baseFile = $file->baseFile)
            throw new NotFoundException(NotFoundException::FileNotFound);

        // todo 处理非本地储存的文件
        if ($baseFile->disk != \Storage::getDefaultDriver())
            throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);

        $path = config('filesystems.disks.local.root') . '/' . $baseFile->path;

        return \Response::download($path, $file->name);
    }

    public function getFilesWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $files = FileModel::query()->paginate($perPage);

        return $this->buildResponse(trans('api.file.paginate.success'), Tools::toArray($files));
    }
}

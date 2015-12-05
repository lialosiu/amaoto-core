<?php namespace App\Http\Controllers\Api;

use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSupportedException;
use App\Exceptions\SecurityException;
use App\File as FileModel;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Image as ImageModel;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

class ImageController extends BaseController
{
    public function doUploadImage(Guard $guard, Request $request)
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
            return $this->buildResponse(trans('api.image.upload.continue'));

        $fileName = $request->get('filename');
        if (!$fileName)
            $fileName = $uploadedFile->getClientOriginalName();

        /** @var User $user */
        $user = $guard->user();

        $file = FileManager::UploadImage($filePath, $fileName, $user);
        return $this->buildResponse(trans('api.image.upload.success'), $file);
    }

    public function getImageBinToShowById($id, $size = null)
    {
        /** @var ImageModel $image */
        $image = ImageModel::whereId($id)->first();
        /** @var FileModel $file */
        $file = null;
        if (!$image)
            return \Response::download('images/image_not_found.png', 'image_not_found.png', [], 'inline');

        switch ($size) {
            case null:
            case 'original':
                if ($image->file)
                    $file = $image->file;
                break;
            case 'thumbnail':
                if ($image->thumbnailFile)
                    $file = $image->thumbnailFile;
                else
                    $file = $image->file;
                break;
            case 'high-resolution':
                if ($image->highResolutionFile)
                    $file = $image->highResolutionFile;
                else
                    $file = $image->file;
                break;
            default:
                // todo 按请求的分辨率缩放
                if ($image->file)
                    $file = $image->file;
                break;
        }

        if (!$file || !$baseFile = $file->baseFile)
            return \Response::download('images/image_not_found.png', 'image_not_found.png', [], 'inline');

        // todo 处理非本地储存的文件
        if ($baseFile->disk != \Storage::getDefaultDriver())
            throw new NotSupportedException(NotSupportedException::FeatureOnTheWay);

        $path = config('filesystems.disks.local.root') . '/' . $baseFile->path;

        return \Response::download($path, $file->name, [], 'inline');
    }

    public function getImageById($id = 0)
    {
        /** @var ImageModel $image */
        $image = ImageModel::whereId($id)->first();

        if (!$image)
            throw new NotFoundException(NotFoundException::ImageNotFound);

        return $this->buildResponse(trans('api.image.get.success'), $image);
    }

    public function getImagesWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $files = ImageModel::query()->paginate($perPage);

        return $this->buildResponse(trans('api.image.paginate.success'), Tools::toArray($files));
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

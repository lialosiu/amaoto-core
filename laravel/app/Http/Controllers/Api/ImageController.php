<?php namespace App\Http\Controllers\Api;

use App\Exceptions\AppException;
use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\File as FileModel;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Image as ImageModel;
use App\Services\FileManager;
use App\Services\Tools;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ImageController extends BaseController
{
    public function doUploadImage(Guard $guard, Request $request)
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
            return $this->buildResponse(trans('api.image.upload.continue'));

        $fileName = $request->get('filename');
        if (!$fileName)
            $fileName = $uploadedFile->getClientOriginalName();

        /** @var User $user */
        $user = $guard->user();

        $image = FileManager::UploadImage($filePath, $fileName, $user);
        return $this->buildResponse(trans('api.image.upload.success'), $image);
    }

    public function getImageBinToDownloadById($id)
    {
        /** @var ImageModel $image */
        $image = ImageModel::where('id', $id)->first();
        if (!$image || !$image->file || !$image->file->baseFile)
            throw new NotFoundException(NotFoundException::ImageNotFound);

        return \Response::download($image->file->baseFile->getLocalCachePath(), $image->file->name);
    }

    public function getImageBinToShowById($id, $size = null)
    {
        /** @var ImageModel $image */
        $image = ImageModel::where('id', $id)->first();
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
            return response()->download('images/image_not_found.png', 'image_not_found.png', [], 'inline');

        return response()->download($baseFile->getLocalCachePath(), $file->name, [], 'inline');
    }

    public function getImageById($id = 0)
    {
        /** @var ImageModel $image */
        $image = ImageModel::where('id', $id)->first();

        if (!$image)
            throw new NotFoundException(NotFoundException::ImageNotFound);

        return $this->buildResponse(trans('api.image.get.success'), $image);
    }

    public function getImagesWithPaginate(Request $request)
    {
        $perPage = $request->get('num');
        if (!is_numeric($perPage) || $perPage < 1 || $perPage > 30)
            $perPage = 15;

        $images = ImageModel::orderBy('id', 'desc')->paginate($perPage);

        return $this->buildResponse(trans('api.image.paginate.success'), Tools::toArray($images));
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

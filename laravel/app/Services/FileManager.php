<?php namespace App\Services;

use App\BasicFile;
use App\Exceptions\FileUploadException;
use App\Exceptions\RequestValidationException;
use App\File as FileModel;
use App\Image as ImageModel;
use App\User;
use Carbon\Carbon;
use File;
use Image;
use Storage;

class FileManager
{
    public static function UploadFile($oriFilePath, $fileName, User $user, $diskName = null)
    {
        $fs = Storage::disk($diskName);

        $fileContent = $fs->get($oriFilePath);
        $mimeType    = $fs->mimeType($oriFilePath);

        $fileModel = self::saveFileByFileContent($fileContent, $fileName, $mimeType, $user, $diskName);

        $fs->delete($oriFilePath);

        return $fileModel;
    }

    public static function UploadImage($oriFilePath, $fileName, User $user, $diskName = null)
    {
        $fs = Storage::disk($diskName);

        $fileContent = $fs->get($oriFilePath);
        $mimeType    = $fs->mimeType($oriFilePath);

        if (!str_is('image/*', $mimeType)) {
            $fs->delete($oriFilePath);
            throw new RequestValidationException(RequestValidationException::FileIsNotImage);
        }

        $fileModel = self::saveFileByFileContent($fileContent, $fileName, $mimeType, $user, $diskName);

        $fs->delete($oriFilePath);

        $image = Image::make($fileContent);

        $thumbnail          = Image::make($fileContent)->widen(100)->encode($fileModel->mime);
        $thumbnailFileModel = FileManager::saveFileByFileContent($thumbnail->encoded, $fileModel->name . '-thumbnail.' . $fileModel->ext, $thumbnail->mime(), $user);

        $highResolution          = Image::make($fileContent)->widen(1000)->encode($fileModel->mime);
        $highResolutionFileModel = FileManager::saveFileByFileContent($highResolution->encoded, $fileModel->name . '-high-resolution.' . $fileModel->ext, $highResolution->mime(), $user);

        $imageModel         = new ImageModel();
        $imageModel->width  = $image->getWidth();
        $imageModel->height = $image->getHeight();
        $imageModel->file()->associate($fileModel);
        $imageModel->thumbnailFile()->associate($thumbnailFileModel);
        $imageModel->highResolutionFile()->associate($highResolutionFileModel);
        $imageModel->save();

        return $imageModel;
    }

    public static function saveFileByFileContent($fileContent, $fileName, $mimeType, User $user, $diskName = null)
    {
        $rootDir = "uploaded-file";

        $fs = Storage::disk($diskName);

        $md5  = md5($fileContent);
        $sha1 = sha1($fileContent);
        $size = strlen($fileContent);

        $diskName = $diskName ? $diskName : Storage::getDefaultDriver();

        $name = $fileName;
        $ext  = File::extension($fileName);

        $now = Carbon::now();

        $toDirPath  = $rootDir . '/' . $now->year . '/' . $now->month . '/' . $now->day;
        $toFilePath = $toDirPath . '/' . $md5 . '-' . $sha1;

        $fs->makeDirectory($toDirPath);

        if (!$fs->exists($toFilePath))
            $fs->put($toFilePath, $fileContent);

        $basicFile = BasicFile::whereMd5($md5)->whereSha1($sha1)->whereSize($size)->first();

        if (!$basicFile) {
            $basicFile       = new BasicFile();
            $basicFile->md5  = $md5;
            $basicFile->sha1 = $sha1;
            $basicFile->size = $size;
            $basicFile->disk = $diskName;
            $basicFile->path = $toFilePath;
            $basicFile->save();
        }

        $fileModel       = new FileModel();
        $fileModel->name = $name;
        $fileModel->ext  = $ext;
        $fileModel->mime = $mimeType;
        $fileModel->user()->associate($user);
        $fileModel->baseFile()->associate($basicFile);

        $fileModel->save();

        return $fileModel;
    }

    public static function rebuildChunkFile($oriFilePath, $uniName, $totalSize)
    {
        $fs = Storage::disk();

        $toDirPath  = 'merging';
        $toFilePath = $toDirPath . '/' . $uniName;

        $fs->makeDirectory($toDirPath);

        if ($fs->exists($toFilePath))
            $fs->put($toFilePath, $fs->get($toFilePath) . File::get($oriFilePath));
        else
            $fs->put($toFilePath, File::get($oriFilePath));

        File::delete($oriFilePath);

        $currentSize = $fs->size($toFilePath);

        if ($currentSize > $totalSize) {
            $fs->delete($toFilePath);
            throw new FileUploadException(FileUploadException::RebuildChunkError);
        }

        if ($currentSize != $totalSize)
            return false;  //还有分块，请求继续发送

        return $toFilePath;
    }

    public static function getMergingFileSize($uniName)
    {
        $fs = Storage::disk();

        $toDirPath  = 'merging';
        $toFilePath = $toDirPath . '/' . $uniName;

        $size = 0;

        if ($fs->exists($toFilePath))
            $size = $fs->size($toFilePath);

        return $size;
    }
}
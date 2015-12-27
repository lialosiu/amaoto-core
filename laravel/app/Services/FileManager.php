<?php namespace App\Services;

use App\BasicFile;
use App\Exceptions\FileUploadException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RequestValidationException;
use App\File as FileModel;
use App\Image as ImageModel;
use App\Music;
use App\User;
use Carbon\Carbon;
use File;
use getID3;
use Image;
use Storage;

class FileManager
{
    public static function UploadFileByTypeCheck($oriFilePath, $fileName, User $user, $diskName = null)
    {
        $fs       = Storage::disk($diskName);
        $mimeType = $fs->mimeType($oriFilePath);

        if (str_is('image/*', $mimeType)) {
            return self::UploadImage($oriFilePath, $fileName, $user, $diskName);
        } else if (str_is('audio/*', $mimeType) || str_is('video/*', $mimeType)) {
            return self::UploadMusic($oriFilePath, $fileName, $user, $diskName);
        }

        return self::UploadFile($oriFilePath, $fileName, $user, $diskName);
    }

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

        if ($image->getWidth() > 1000) {
            $highResolution          = Image::make($fileContent)->widen(1000)->encode($fileModel->mime);
            $highResolutionFileModel = FileManager::saveFileByFileContent($highResolution->encoded, $fileModel->name . '-high-resolution.' . $fileModel->ext, $highResolution->mime(), $user);
        } else {
            $highResolution          = Image::make($fileContent)->widen($image->getWidth())->encode($fileModel->mime);
            $highResolutionFileModel = FileManager::saveFileByFileContent($highResolution->encoded, $fileModel->name . '-high-resolution.' . $fileModel->ext, $highResolution->mime(), $user);
        }

        $imageModel         = new ImageModel();
        $imageModel->width  = $image->getWidth();
        $imageModel->height = $image->getHeight();
        $imageModel->file()->associate($fileModel);
        $imageModel->thumbnailFile()->associate($thumbnailFileModel);
        $imageModel->highResolutionFile()->associate($highResolutionFileModel);
        $imageModel->save();

        return $imageModel;
    }

    public static function UploadMusic($oriFilePath, $fileName, User $user, $diskName = null)
    {
        $fs = Storage::disk($diskName);

        $fileContent = $fs->get($oriFilePath);
        $mimeType    = $fs->mimeType($oriFilePath);

        if (!str_is('audio/*', $mimeType) && !str_is('video/*', $mimeType)) {
            $fs->delete($oriFilePath);
            throw new RequestValidationException(RequestValidationException::FileIsNotMusic);
        }

        $fileModel = self::saveFileByFileContent($fileContent, $fileName, $mimeType, $user, $diskName);

        $fs->delete($oriFilePath);

        $getId3Result = self::analyzeFileByGetId3($fileModel);

        // 标签数据
        if ($getId3Result && isset($getId3Result['tags'])) {
            $types = ['quicktime', 'ape', 'id3v2', 'id3v1'];
            foreach ($types as $type) {
                if (!isset($getId3Result['tags'][$type]))
                    continue;
                $tag = $getId3Result['tags'][$type];

                $tag_title  = implode(';', isset($tag['title']) ? $tag['title'] : []);
                $tag_artist = implode(';', isset($tag['artist']) ? $tag['artist'] : []);
                $tag_album  = implode(';', isset($tag['album']) ? $tag['album'] : []);
                $tag_year   = implode(';', isset($tag['creation_date']) ? $tag['creation_date'] : (isset($tag['year']) ? $tag['year'] : []));
                $tag_track  = implode(';', isset($tag['track_number']) ? $tag['track_number'] : (isset($tag['track']) ? $tag['track'] : []));
                $tag_genre  = implode(';', isset($tag['genre']) ? $tag['genre'] : []);
//                $tag_comment      = implode(';', isset($tag['comment']) ? $tag['comment'] : []);
                $tag_album_artist = implode(';', isset($tag['album_artist']) ? $tag['album_artist'] : []);
                $tag_composer     = implode(';', isset($tag['composer']) ? $tag['composer'] : []);
//                $tag_disc_number  = implode(';', isset($tag['disc_number']) ? $tag['disc_number'] : []);

                break;
            }
        }


        // 封面图
        if ($getId3Result
            && isset($getId3Result['comments'])
            && isset($getId3Result['comments']['picture'])
            && isset($getId3Result['comments']['picture']['0'])
            && isset($getId3Result['comments']['picture']['0']['data'])
        ) {
            $coverData     = $getId3Result['comments']['picture']['0']['data'];
            $coverTempFile = FileManager::saveTempFileByFileContent($coverData, $user);
//            $coverFileGetId3Result = self::analyzeFileByGetId3($coverTempFile);
            $coverFileName = sprintf('%s-cover-file', (isset($tag_title) && $tag_title) ? $tag_title : $fileName);

            $coverImage = self::UploadImage($coverTempFile->baseFile->path, $coverFileName, $user);
        }


        $music        = new Music();
        $music->title = (isset($tag_title) && $tag_title) ? $tag_title : $fileModel->name;

        if (isset($tag_artist) && $tag_artist) {
            $music->artist = $tag_artist;
        } else if (isset($tag_composer) && $tag_composer) {
            $music->artist = $tag_composer;
        } else if (isset($tag_album_artist) && $tag_album_artist) {
            $music->artist = $tag_album_artist;
        } else {
            $music->artist = null;
        }

        $music->year = (isset($tag_year) && $tag_year) ? $tag_year : null;

        if (isset($tag_track) && $tag_track) {
            $tmp          = explode('/', $tag_track);
            $trackNum     = (is_array($tmp) && sizeof($tmp) > 0) ? $tmp[0] : $tag_track;
            $music->track = $trackNum;
        }

        $music->genre    = (isset($tag_genre) && $tag_genre) ? $tag_genre : null;
        $music->playtime = isset($getId3Result['playtime_seconds']) ? $getId3Result['playtime_seconds'] : null;
        $music->bitrate  = isset($getId3Result['bitrate']) ? $getId3Result['bitrate'] : null;

        $music->album_title  = (isset($tag_album) && $tag_album) ? $tag_album : null;
        $music->album_artist = (isset($tag_album_artist) && $tag_album_artist) ? $tag_album_artist : null;
        $music->tags         = ($getId3Result && isset($getId3Result['tags'])) ? json_encode($getId3Result['tags']) : null;

        $music->file()->associate($fileModel);

        if (isset($coverImage) && $coverImage)
            $music->coverImage()->associate($coverImage);

        $music->save();

        return $music;
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

        $basicFile = BasicFile::where('md5', $md5)->where('sha1', $sha1)->where('size', $size)->first();

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

    public static function saveTempFileByFileContent($fileContent, User $user, $diskName = null)
    {
        $rootDir = "temp-file";

        $fs = Storage::disk($diskName);

        $md5  = md5($fileContent);
        $sha1 = sha1($fileContent);
        $size = strlen($fileContent);

        $diskName = $diskName ? $diskName : Storage::getDefaultDriver();

        $name = $md5 . '-' . $sha1 . '-' . str_random();
        $ext  = '.tmp';

        $now = Carbon::now();

        $toDirPath  = $rootDir . '/' . $now->year . '/' . $now->month . '/' . $now->day;
        $toFilePath = $toDirPath . '/' . $md5 . '-' . $sha1;

        $fs->makeDirectory($toDirPath);

        if (!$fs->exists($toFilePath))
            $fs->put($toFilePath, $fileContent);

        $basicFile       = new BasicFile();
        $basicFile->md5  = $md5;
        $basicFile->sha1 = $sha1;
        $basicFile->size = $size;
        $basicFile->disk = $diskName;
        $basicFile->path = $toFilePath;

        $fileModel       = new FileModel();
        $fileModel->name = $name;
        $fileModel->ext  = $ext;
        $fileModel->mime = '';
        $fileModel->user()->associate($user);
        $fileModel->baseFile()->associate($basicFile);

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

    public static function analyzeFileByGetId3($file, $encoding_id3v1 = 'GBK')
    {
        if (!$file || !$file->baseFile)
            throw new NotFoundException(NotFoundException::FileNotFound);

        $getId3                 = new getID3();
        $getId3->encoding_id3v1 = $encoding_id3v1;
        $path                   = $file->baseFile->getLocalCachePath();
        return $getId3->analyze($path);
    }
}
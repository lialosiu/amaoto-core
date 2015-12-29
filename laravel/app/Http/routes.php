<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::any('test', function () {
    phpinfo();
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    Route::group(['prefix' => 'system', 'middleware' => []], function () {
        Route::get('info', ['uses' => 'Api\SystemController@getInfo', 'middleware' => []]);
        Route::post('save-setting', ['uses' => 'Api\SystemController@doSaveSetting', 'middleware' => ['auth.admin']]);
    });

    Route::group(['prefix' => 'auth', 'middleware' => []], function () {
        Route::post('sign-up', ['uses' => 'Api\AuthController@doSignUp', 'middleware' => []]);
        Route::post('sign-in', ['uses' => 'Api\AuthController@doSignIn', 'middleware' => []]);
        Route::get('current-user', ['uses' => 'Api\AuthController@getCurrentUser', 'middleware' => []]);
    });

    Route::group(['prefix' => 'user', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\UserController@getUsersWithPaginate', 'middleware' => ['auth.admin']]);
    });

    Route::group(['prefix' => 'file', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\FileController@getFilesWithPaginate', 'middleware' => ['auth.admin']]);
        Route::post('upload', ['uses' => 'Api\FileController@doUploadFile', 'middleware' => ['auth.admin']]);
        Route::get('uploaded-size', ['uses' => 'Api\FileController@getUploadedFileSize', 'middleware' => ['auth.admin']]);
        Route::get('download/{id}', ['uses' => 'Api\FileController@getFileBinToDownloadById', 'middleware' => []]);
        Route::get('show/{id}', ['uses' => 'Api\FileController@getFileBinToShowById', 'middleware' => []]);
        Route::get('{id}', ['uses' => 'Api\FileController@getFileById', 'middleware' => []]);
    });

    Route::group(['prefix' => 'image', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\ImageController@getImagesWithPaginate', 'middleware' => []]);
        Route::post('upload', ['uses' => 'Api\ImageController@doUploadImage', 'middleware' => ['auth.admin']]);
        Route::get('uploaded-size', ['uses' => 'Api\ImageController@getUploadedFileSize', 'middleware' => ['auth.admin']]);
        Route::get('show/{id}/{size?}', ['uses' => 'Api\ImageController@getImageBinToShowById', 'middleware' => []]);
        Route::get('download/{id}', ['uses' => 'Api\ImageController@getImageBinToDownloadById', 'middleware' => []]);
        Route::get('{id}', ['uses' => 'Api\ImageController@getImageById', 'middleware' => []]);
    });

    Route::group(['prefix' => 'music', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\MusicController@getMusicsWithPaginate', 'middleware' => []]);
        Route::post('upload', ['uses' => 'Api\MusicController@doUploadMusic', 'middleware' => ['auth.admin']]);
        Route::get('uploaded-size', ['uses' => 'Api\MusicController@getUploadedFileSize', 'middleware' => ['auth.admin']]);
        Route::get('show/{id}/{size?}', ['uses' => 'Api\MusicController@getMusicBinToShowById', 'middleware' => []]);
        Route::get('download/{id}', ['uses' => 'Api\MusicController@getMusicBinToDownloadById', 'middleware' => []]);
        Route::get('{id}', ['uses' => 'Api\MusicController@getMusicById', 'middleware' => []]);
    });

    Route::group(['prefix' => 'album', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\AlbumController@getAlbumsWithPaginate', 'middleware' => []]);
        Route::post('create', ['uses' => 'Api\AlbumController@doCreateAlbum', 'middleware' => ['auth.admin']]);
    });
});

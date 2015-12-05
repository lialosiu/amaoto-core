<?php

Route::any('test', function () {
    return response()->json(Input::all());
});

Route::group(['prefix' => 'api', 'middleware' => []], function () {
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
        Route::get('download/{id}', ['uses' => 'Api\FileController@getFileBinToDownloadById', 'middleware' => ['auth.admin']]);
    });

    Route::group(['prefix' => 'image', 'middleware' => []], function () {
        Route::get('paginate', ['uses' => 'Api\ImageController@getImagesWithPaginate', 'middleware' => ['auth.admin']]);
        Route::post('upload', ['uses' => 'Api\ImageController@doUploadImage', 'middleware' => ['auth.admin']]);
        Route::get('show/{id}/{size?}', ['uses' => 'Api\ImageController@getImageBinToShowById', 'middleware' => ['auth.admin']]);
        Route::get('uploaded-size', ['uses' => 'Api\ImageController@getUploadedFileSize', 'middleware' => ['auth.admin']]);
        Route::get('{id}', ['uses' => 'Api\ImageController@getImageById', 'middleware' => ['auth.admin']]);
    });
});

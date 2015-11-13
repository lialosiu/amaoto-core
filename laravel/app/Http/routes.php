<?php

Route::group(['prefix' => 'api', 'middleware' => []], function () {
    Route::group(['prefix' => 'system', 'middleware' => []], function () {
        Route::get('info', 'Api\SystemController@getInfo');
        Route::post('save-setting', 'Api\SystemController@doSaveSetting');
    });

    Route::group(['prefix' => 'auth', 'middleware' => []], function () {
        Route::post('sign-up', 'Api\AuthController@doSignUp');
        Route::post('sign-in', 'Api\AuthController@doSignIn');
    });

    Route::group(['prefix' => 'oa-user', 'middleware' => []], function () {
        Route::post('create', 'Api\OaUserController@doCreateOaUser');
        Route::post('edit', 'Api\OaUserController@doEditOaUser');
    });

    Route::group(['prefix' => 'department', 'middleware' => []], function () {
        Route::post('create', 'Api\DepartmentController@doCreateDepartment');
    });

    Route::group(['prefix' => 'position', 'middleware' => []], function () {
        Route::post('create', 'Api\PositionController@doCreatePosition');
    });

    Route::group(['prefix' => 'file', 'middleware' => []], function () {
        Route::post('upload', 'Api\FileController@doUploadFile');
        Route::get('download/{id}', 'Api\FileController@getFileBinToDownloadById');
    });

    Route::group(['prefix' => 'image', 'middleware' => []], function () {
        Route::post('upload', 'Api\ImageController@doUploadImage');
        Route::get('show/{id}/{size?}', 'Api\ImageController@getImageBinToShowById');
        Route::get('{id}', 'Api\ImageController@getImageById');
    });
});

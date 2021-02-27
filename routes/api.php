<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth',
    'middleware' => ['api', 'cors'],
], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('signup', 'AuthController@signup');
});

Route::group([
    'middleware' => ['addAccessToken', 'auth:api', 'cors'],
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::any('logout', 'AuthController@logout');
    });
    //admin
    Route::group([
        'middleware' => 'check_user_role:' . \App\Role\UserRole::ROLE_MANAGER,
    ], function () {
        Route::resource('users', 'UserController');
        Route::get('newusers', 'UserController@newUsers');
    });
    // Route::resource('users', 'UserController')->middleware('check_user_role:' . \App\Role\UserRole::ROLE_MANAGER);
    // Route::get('newusers', 'UserController@newUsers')->middleware('check_user_role:' . \App\Role\UserRole::ROLE_MANAGER);

    //user
    Route::get('/profile/me', 'UserController@myProfile');
    Route::patch('/profile/me', 'UserController@updateMyProfile');
    Route::get('/accounts', 'AccountController@getMyAccounts');
    Route::post('/accounts', 'AccountController@addMyAccounts');
    Route::post('/sources', 'SourceController@addSource');

    Route::post('/checksources', 'AccountController@checkAccount');
    Route::get('/providesources', 'AccountController@getProvideAccount');
    Route::post('/providesources', 'AccountController@provideAccount');
    Route::get('/accounts-for-provide', 'AccountController@getAccountsForProvide');

    Route::post('/signaldetail', 'SourceController@getProvideSourceDetail');

    Route::post('/signaldetailwithtime', 'SourceController@getProvideSourceDetailWithTime');

    Route::get('/availablesources', 'AccountController@getAvailableSignal');

    Route::get('/copysources', 'AccountController@getCopyAccount');
    Route::get('/accounts-for-copy', 'AccountController@getAccountsForCopy');
    Route::post('/copysources', 'AccountController@copyAccount');
    Route::post('/copysetting', 'AccountController@getCopySetting');
    Route::post('/savecopysetting', 'AccountController@saveCopySetting');

    Route::delete('/providesource/{id}', 'AccountController@deleteProvideAccount');
    Route::delete('/copysource/{id}', 'AccountController@deleteCopyAccount');
    Route::post('/deletesources', 'SourceController@deleteSources');
});

Route::group([
    'middleware' => ['api', 'cors'],
], function () {
    Route::any('/{any}', function () {
        return response()->json([
            'response' => [
                'api_status' => 0,
                'code' => 404,
                'message' => 'not found'
            ]
        ], 404);
    })->where('any', '.*');
});

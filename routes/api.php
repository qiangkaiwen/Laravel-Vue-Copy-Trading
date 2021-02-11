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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'prefix' => 'auth',
    'middleware' => 'api',
], function () {
    Route::any('login', 'AuthController@login')->name('login');
    Route::any('signup', 'AuthController@signup');
});

Route::group([
    'middleware' => 'auth:api'
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::any('logout', 'AuthController@logout');
    });
    //admin
    Route::resource('users', 'UserController');
    Route::get('/accounts/{user_id}', 'AccountController@getAccounts');
    Route::post('/accounts/{user_id}', 'AccountController@addAccounts');

    //user
    Route::get('/accounts', 'AccountController@getMyAccounts');
    Route::post('/accounts', 'AccountController@addMyAccounts');
    Route::post('/sources', 'SourceController@addSource');
});

Route::any('/{any}', function () {
    return response()->json([
        'response' => [
            'api_status' => 0,
            'code' => 404,
            'message' => 'not found'
        ]
    ], 404);
})->where('any', '.*');

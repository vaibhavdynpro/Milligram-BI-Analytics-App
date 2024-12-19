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

Route::post('login', [
    	'as' => 'auth.login',
    	'uses' =>'API\v1\AuthController@login'
    ]);
Route::post('me', [
    	'as' => 'auth.me',
    	'uses' =>'AuthController@me'
    ]);
Route::post('v1/909_hello', [
    	'as' => 'auth.decodejwt',
    	'uses' =>'API\v1\AuthController@decodejwt_v1'
    ])->middleware('log.route');
Route::post('v2/organization_kpi', [
        'as' => 'auth.decodejwt',
        'uses' =>'API\v1\AuthController@decodejwt'
    ])->middleware('log.route');
Route::post('verifyjwt', [
        'as' => 'auth.verifyjwt',
        'uses' =>'AuthController@verifyjwt'
    ]);
Route::post('dashbord', [
    	'as' => 'dashbord',
    	'uses' =>'API\HomeController@dashbord'
    ]);
Route::post('v2/sso_embed_url', [
        'as' => 'auth.sso_embed_url',
        'uses' =>'API\v1\AuthController@iframe'
    ])->middleware('log.route');
Route::post('v1/sso_embed_url', [
        'as' => 'auth.sso_embed_url',
        'uses' =>'API\v1\AuthController@iframe_v1'
    ])->middleware('log.route');
// Route::post('user', [
//         'as' => 'users.user',
//         'uses' =>'API\v1\UsersController@create'
//     ]);
// Route::apiResource('v1/user', 'API\v1\UsersController');
Route::post('v2/user/create', 'API\v1\UsersController@store');
Route::post('v2/user/update', 'API\v1\UsersController@update');
Route::post('v2/user/list', 'API\v1\UsersController@list');

// Route::apiResource('v1/client', 'API\v1\ClientController');
Route::post('v2/organization/create', 'API\v1\ClientController@store');
Route::post('v2/organization/update', 'API\v1\ClientController@update');
Route::post('v2/organization/list', 'API\v1\ClientController@list');

Route::apiResource('v1/user_client', 'API\v1\UserClientController');
Route::post('v1/user_client/list', 'API\v1\UserClientController@list');
Route::post('v1/user_client/getdata', 'API\v1\UserClientController@getdata');
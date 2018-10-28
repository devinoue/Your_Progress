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

Route::group(['middleware' => ['api']], function(){
    Route::get('/', 'YourProgressController@index');//表示
    Route::post('/', 'YourProgressController@store');//新規登録
    Route::patch('/{id_name}', 'YourProgressController@update');//更新
    Route::delete('/{id_name}', 'YourProgressController@destroy');//削除
});
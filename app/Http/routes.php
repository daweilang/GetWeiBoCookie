<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin' ], function (){
	
	Route::get('/', 'HomeController@index');
	//获得微博授权相关
	Route::get('authorize', 'AuthorizeController@index');
	Route::post('authorize/setConfig', 'AuthorizeController@setConfig');
	Route::get('authorize/getPreParam', 'AuthorizeController@getPreParam');
	Route::get('authorize/getRsaPwd', 'AuthorizeController@getRsaPwd');
	Route::get('authorize/browserLogin', 'AuthorizeController@browserLogin');
	Route::post('authorize/getCookie', 'AuthorizeController@getCookie');

	Route::get('authorize/setTestUrl', 'AuthorizeController@setTestUrl');
	Route::post('authorize/getTestContent', 'AuthorizeController@getTestContent');
	
	//返回提示成功信息
	Route::get('authorize/seccuss', function(){return view('admin/seccuss');});
	Route::get('authorize/fail', function(){return view('admin/fail');});
});
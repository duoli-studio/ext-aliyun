<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('test', 'HomeController@getTest');
Route::post('test', 'HomeController@postTest');
Route::get('/', [
	'as'   => 'home.homepage',
	'uses' => 'HomeController@getHomepage',
]);
Route::get('cp', [
	'as'   => 'home.cp',
	'uses' => 'HomeController@getCp',
]);
/*
Route::controller('home', 'HomeController', [
	'getTest' => 'home.test',
	'getCp'   => 'home.cp',
]);
*/

// 便于找错, 统一到一个上边
\Route::match(['get', 'post'], 'notify/mama', [
	'as'   => 'notify.mama',
	'uses' => 'NotifyController@mama',
]);
\Route::match(['get', 'post'], 'notify/yi', [
	'as'   => 'notify.yi',
	'uses' => 'NotifyController@yi',
]);
\Route::match(['get', 'post'], 'notify/mao', [
	'as'   => 'notify.mao',
	'uses' => 'NotifyController@mao',
]);
\Route::match(['get', 'post'], 'notify/tong', [
	'as'   => 'notify.tong',
	'uses' => 'NotifyController@tong',
]);

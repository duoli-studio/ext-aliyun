<?php

/**
 * Copyright (C) Update For IDE
 */

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

Route::group(['prefix' => 'dev'], function () {

	Route::get('cp', [
		'uses' => 'HomeController@getCp',
		'as'   => 'dev_home.cp',
	]);
	Route::get('/', [
		'uses' => 'HomeController@getCp',
		'as'   => 'dev_home.cp',
	]);
	/*
	Route::controller('dev_home', 'HomeController', [
		'getCp'     => 'dev_home.cp',
		'getLogin'  => 'dev_home.login',
		'postLogin' => 'dev_home.login',
	]);
	Route::controller('dev_env', 'EnvController', [
		'getRun'  => 'dev_env.run',
		'getInfo' => 'dev_env.info',
	]);
	*/
});

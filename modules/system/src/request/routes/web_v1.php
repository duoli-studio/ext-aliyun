<?php
/*/*
|--------------------------------------------------------------------------
| Util
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'middleware' => ['cross'],
	'prefix'     => 'util',
	'namespace'  => 'System\Request\ApiV1\Util',
], function(Illuminate\Routing\Router $route) {
	// 获取图像和地区代码
	$route->get('/captcha/image', 'CaptchaController@image');
	$route->get('/area/code', 'AreaController@code');

	// 发送验证码
	$route->post('/captcha/send', 'CaptchaController@send');
	$route->post('/captcha/verify_code', 'CaptchaController@verifyCode');

	// 只要是用户就可以
	$route->group([
		'middleware' => ['auth:jwt', 'disabled_pam'],
	], function(Illuminate\Routing\Router $route) {
		$route->post('/image/upload', 'ImageController@upload');
		$route->post('/approve/status', 'ApproveController@approveStatus');
	});

});

/*
|--------------------------------------------------------------------------
| Pam
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'middleware' => ['cross'],
	'prefix'     => 'pam',
	'namespace'  => 'System\Request\ApiV1\Pam',
], function(Illuminate\Routing\Router $route) {
	// auth
	$route->post('auth/access', 'AuthController@access')->name('system:pam.auth.access');
	$route->post('auth/token/{guard}', 'AuthController@token')->name('system:pam.auth.token');
	$route->post('auth/reset_password', 'AuthController@resetPassword');
	$route->post('auth/login', 'AuthController@login');

	$route->group([
		'middleware' => ['auth:jwt', 'disabled_pam'],
	], function(Illuminate\Routing\Router $route) {
		// bind
		$route->post('bind/cancel', 'BindController@cancel');
		$route->post('bind/new_passport', 'BindController@newPassport');
	});
});
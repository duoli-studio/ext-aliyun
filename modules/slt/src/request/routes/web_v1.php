<?php

/*/*
|--------------------------------------------------------------------------
| Util
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'middleware' => ['cross'],
	'namespace'  => 'Slt\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
	$route->post('xmlrpc', 'XmlRpcController@on');
});

/*
|--------------------------------------------------------------------------
| slt 图片上传
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'middleware' => ['cross'],
], function (Illuminate\Routing\Router $route) {
	$route->post('image/upload', '\System\Request\ApiV1\Util\ImageController@upload')
		->name('slt:image.upload');
});

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
], function(Illuminate\Routing\Router $route) {
	// 获取图像和地区代码
	$route->post('xmlrpc', 'XmlRpcController@on');
});
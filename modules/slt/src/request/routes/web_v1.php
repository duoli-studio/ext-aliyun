<?php
/*/*
|--------------------------------------------------------------------------
| Util
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'namespace' => 'Slt\Request\Web',
], function(Illuminate\Routing\Router $route) {
	$route->get('/', 'HomeController@index')
		->name('slt');
	$route->get('tool/{type?}', 'ToolController@index')
		->name('slt:tool');
	$route->post('util/image', 'UtilController@image')
		->name('slt:util.image');

	$route->get('fe/js/{name?}', 'FeController@js')
		->name('slt:fe.js');
	\Route::get('fe/md', 'FeController@markdown')
		->name('slt:fe.md');

	// user
	$route->any('user/login', 'UserController@login')
		->name('slt:user.login');
	$route->any('user/register/{type?}', 'UserController@register')
		->name('slt:user.register');
	$route->any('user/forgot_password', 'UserController@getForgotPassword')
		->name('slt:user.forgot_password');
	$route->group([
		'middleware' => 'auth:web',
	], function(Illuminate\Routing\Router $route) {
		$route->any('user/profile', 'UserController@profile')
			->name('slt:user.profile');
		$route->any('user/nickname', 'UserController@nickname')
			->name('slt:user.nickname');
		$route->any('user/avatar', 'UserController@avatar')
			->name('slt:user.avatar');
		$route->any('user/logout', 'UserController@logout')
			->name('slt:user.logout');


		/* Nav Url
		 -------------------------------------------- */
		$route->any('nav', 'NavController@index')
			->name('slt:nav.index');
		$route->get('nav/jump/{id?}', 'NavController@jump')
			->name('slt:nav.jump');
		$route->get('nav/jump_user/{id?}', 'NavController@jumpUser')
			->name('slt:nav.jump_user');
		$route->any('nav/collection/{id?}', 'NavController@collection')
			->name('slt:nav.collection');
		$route->any('nav/collection_destroy/{id?}', 'NavController@collectionDestroy')
			->name('slt:nav.collection_destroy');
		$route->any('nav/url/{id?}', 'NavController@url')
			->name('slt:nav.url');
		$route->any('nav/url_destroy/{id?}', 'NavController@urlDestroy')
			->name('slt:nav.url_destroy');
		$route->any('nav/fetch-title', 'NavController@fetchTitle')
			->name('slt:nav.fetch_title');
		$route->any('nav/tag', 'NavController@tag')
			->name('slt:nav.tag');
	});

});
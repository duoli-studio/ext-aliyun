<?php

/*
|--------------------------------------------------------------------------
| #Rules# 路由命名规则
|--------------------------------------------------------------------------
|  1. 前缀.分类.do
|  2. 前缀/分类 均为一个单词, do 可以为多个单词的蛇形排列
|  3. 路由地址为 路由命名规则, 然后 . 替换为 /
|  4. 路由简称 [be|后端(backend);web|前端(FrontEnd);h5|手机端页面;app|App嵌入页面;api|应用接口]
*/
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

\Route::group([
	'prefix' => 'slt',
], function() {

	\Route::group([
		'middleware' => 'web',
	], function() {

		\Route::any('fe/editor/{id?}', 'Web\FeController@editor')
			->name('web:fe.editor');
		\Route::post('fe/run', 'Web\FeController@run')
			->name('web:fe.run');
		Route::any('fe/react/{book}', 'Web\FeController@react')
			->name('web:fe.react');



		Route::any('nav/my_book_item/{id?}', 'Web\PrdController@book')
			->name('web:prd.book');


		\Route::any('user/profile', 'Web\UserController@profile')
			->name('web:user.profile');
		\Route::any('user/nickname', 'Web\UserController@nickname')
			->name('web:user.nickname');
		\Route::any('user/avatar', 'Web\UserController@getAvatar')
			->name('web:user.avatar');
		\Route::any('user/logout', 'Web\UserController@logout')
			->name('web:user.logout');


		Route::any('nav', 'Web\NavController@index')
			->name('web:nav.index');
		Route::get('nav/jump/{id?}', 'Web\NavController@jump')
			->name('web:nav.jump');
		Route::get('nav/jump_user/{id?}', 'Web\NavController@jumpUser')
			->name('web:nav.jump_user');
		Route::any('nav/collection/{id?}', 'Web\NavController@collection')
			->name('web:nav.collection');
		Route::any('nav/collection_destroy/{id?}', 'Web\NavController@collectionDestroy')
			->name('web:nav.collection_destroy');
		Route::any('nav/url/{id?}', 'Web\NavController@url')
			->name('web:nav.url');
		Route::any('nav/url_destroy/{id?}', 'Web\NavController@urlDestroy')
			->name('web:nav.url_destroy');
		Route::any('nav/title', 'Web\NavController@title')
			->name('web:nav.title');
		Route::any('nav/tag', 'Web\NavController@tag')
			->name('web:nav.tag');
	});

	// upload
	\Route::any('util/image', 'Web\UtilController@image')
		->name('web:util.image');

	// user
	\Route::any('user/login', 'Web\UserController@login')
		->name('web:user.login');
	\Route::any('user/register/{type?}', 'Web\UserController@register')
		->name('web:user.register');
	\Route::any('user/forgot_password', 'Web\UserController@getForgotPassword')
		->name('web:user.forgot_password');

	// tool
	\Route::any('tool/{type?}', 'Web\ToolController@index')
		->name('web:tool.index');

	// home
	\Route::get('/', 'HomeController@index')
		->name('web:home.index');
	\Route::get('/vue', 'HomeController@vue')
		->name('slt:home.vue');


	/*
	Route::controller('prd', 'PrdController', [
		'getShow'          => 'front_prd.show',
		'postStatusTrash'  => 'front_prd.status_trash',
		'postStatusDelete' => 'front_prd.status_delete',
		'postStatusDraft'  => 'front_prd.status_draft',
		'postStatusPost'   => 'front_prd.status_post',
		'getName'          => 'front_prd.name',
		'getAddress'       => 'front_prd.address',
		'getView'          => 'front_prd.view',
		'getViewName'      => 'front_prd.view_name',
		'getDetail'        => 'front_prd.detail',
		'postGood'         => 'front_prd.good',
		'postBad'          => 'front_prd.bad',
	]);
	*/

	// dailian
	Route::get('test', 'Web\TestController@index')
		->name('web:test.index');
});


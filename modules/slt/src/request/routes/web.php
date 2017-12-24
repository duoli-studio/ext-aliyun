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
], function () {

	\Route::group([
		'middleware' => 'web',
	], function () {
		// fe
		\Route::get('fe/js/{name?}', 'Web\FeController@js')
			->name('web:fe.js');
		\Route::get('fe/md', 'Web\FeController@markdown')
			->name('web:fe.md');
		\Route::any('fe/editor/{id?}', 'Web\FeController@editor')
			->name('web:fe.editor');
		\Route::post('fe/run', 'Web\FeController@run')
			->name('web:fe.run');
		Route::any('fe/react/{book}', 'Web\FeController@react')
			->name('web:fe.react');

		\Route::any('prd/create', 'Web\PrdController@create')
			->name('web:prd.create');
		\Route::any('prd/popup/{id?}', 'Web\PrdController@popup')
			->name('web:prd.popup');
		\Route::any('prd/content/{id}', 'Web\PrdController@content')
			->name('web:prd.content');
		\Route::any('prd/my_book', 'Web\PrdController@myBook')
			->name('web:prd.my_book');
		\Route::any('prd/show/{id}', 'Web\PrdController@show')
			->name('web:prd.show');
		\Route::any('prd/destroy/{id}', 'Web\PrdController@destroy')
			->name('web:prd.destroy');
		\Route::any('prd/access/{id}', 'Web\PrdController@access')
			->name('web:prd.access');
		\Route::any('prd/address/{id}', 'Web\PrdController@address')
			->name('web:prd.address');
		\Route::any('prd/status/{id}/{type}', 'Web\PrdController@status')
			->name('web:prd.status');
		Route::any('prd/my_book_item/{id?}', 'Web\PrdController@myBookItem')
			->name('web:prd.my_book_item');

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

// xmlrpc
	\Route::any('xmlrpc', 'Web\XmlRpcController@on')
		->name('web:xmlrpc.on');

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
// tgp
	Route::get('tgp/fw', 'Web\TgpController@fw')
		->name('web:tgp.fw');
	Route::any('tgp/search', 'Web\TgpController@search')
		->name('web:tgp.search');
	Route::get('tgp/zj', 'Web\TgpController@zj')
		->name('web:tgp.zj');
	Route::get('tgp/get_player_mastery_spell', 'Web\TgpController@getPlayerMasterySpell')
		->name('web:tgp.player_mastery_spell');
	Route::get('tgp/tcall', 'Web\TgpController@tcall')
		->name('web:tgp.tcall');
	Route::get('tgp/player', 'Web\TgpController@player')
		->name('web:tgp.player');
	Route::get('tgp/tgp_info', 'Web\TgpController@tgpInfo')
		->name('web:tgp.tgp_info');
	Route::get('tgp/search_player', 'Web\TgpController@searchPlayer')
		->name('web:tgp.tgp_search_player');
	Route::get('tgp/get_user_hot_info', 'Web\TgpController@getUserHotInfo')
		->name('web:tgp.get_user_hot_info');
	Route::get('tgp/core/{type}', 'Web\TgpController@core')
		->name('web:tgp.core');


// dailian
	Route::get('test', 'Web\TestController@index')
		->name('web:test.index');
	\Route::get('l5_log', '\Duoli\L5Log\Http\L5LogController@index')
		->name('duoli.l5_log');
	\Route::get('l5_api_doc', '\Duoli\L5ApiDoc\Http\L5ApiDocController@auto')
		->name('duoli.l5_api_doc');

});


<?php
/*
 * 前台路由
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2015 lemon team
 * @time       2015/10/7 23:00
 */

Route::match(['get'], '/', 'HomeController@layout')
	->name('backend:backend.home.layout');

Route::match(['get', 'post'], 'dsk_lemon_home/login', 'LemonHomeController@login')
	->name('dsk_lemon_home.login');

Route::group([
	'middleware' => 'backend.auth',
], function () {

	// home
	Route::get('dsk_cp', [
		'as'   => 'dsk_lemon_home.cp',
		'uses' => 'LemonHomeController@getCp',
	]);

	Route::get('dsk_lemon_home/welcome', 'LemonHomeController@getWelcome')
		->name('dsk_lemon_home.welcome');


	Route::any('lemon_site/setting', 'LemonSiteController@setting')
		->name('dsk_lemon_site.setting');
	Route::any('lemon_site/cache', 'LemonSiteController@cache')
		->name('dsk_lemon_site.cache');

	/*
	// 主页
	Route::controller('dsk_lemon_home', 'LemonHomeController', [
		'getWelcome'   => 'dsk_lemon_home.welcome',
		'getTip'       => 'dsk_lemon_home.tip',
		'getTest'      => 'dsk_lemon_home.test',
		'getLogout'    => 'dsk_lemon_home.logout',
		'getPassword'  => 'dsk_lemon_home.password',
		'postPassword' => 'dsk_lemon_home.password',
		'getLogin'     => 'dsk_lemon_home.login',
		'postLogin'    => 'dsk_lemon_home.login',
	]);

	// 网站设置
	Route::controller('dsk_lemon_site', 'LemonSiteController', [
		'getSetting' => 'dsk_lemon_site.setting',
		'postCache'  => 'dsk_lemon_site.cache',
	]);

	// 游戏名称
	Route::controller('dsk_game_name', 'GameNameController', [
		'getIndex'    => 'dsk_game_name.index',
		'getCreate'   => 'dsk_game_name.create',
		'postCreate'  => 'dsk_game_name.create',
		'getEdit'     => 'dsk_game_name.edit',
		'postEdit'    => 'dsk_game_name.edit',
		'postDestroy' => 'dsk_game_name.destroy',
	]);

	// 游戏类型
	Route::controller('dsk_game_type', 'GameTypeController', [
		'getIndex'    => 'dsk_game_type.index',
		'getCreate'   => 'dsk_game_type.create',
		'postCreate'  => 'dsk_game_type.create',
		'getEdit'     => 'dsk_game_type.edit',
		'postEdit'    => 'dsk_game_type.edit',
		'postDestroy' => 'dsk_game_type.destroy',
		'postSort'    => 'dsk_game_type.sort',
		'postStatus'  => 'dsk_game_type.status',
	]);

	// 游戏来源
	Route::controller('dsk_game_source', 'GameSourceController', [
		'getIndex'    => 'dsk_game_source.index',
		'getCreate'   => 'dsk_game_source.create',
		'postCreate'  => 'dsk_game_source.create',
		'getEdit'     => 'dsk_game_source.edit',
		'postEdit'    => 'dsk_game_source.edit',
		'postDestroy' => 'dsk_game_source.destroy',
		'postSort'    => 'dsk_game_source.sort',
	]);

	// 游戏服务器
	Route::controller('dsk_game_server', 'GameServerController', [
		'postStatus'  => 'dsk_game_server.status',
		'getIndex'    => 'dsk_game_server.index',
		'postSort'    => 'dsk_game_server.sort',
		'getProgress' => 'dsk_game_server.progress',
		'getEdit'     => 'dsk_game_server.edit',
		'getCreate'   => 'dsk_game_server.create',
		'postDestroy' => 'dsk_game_server.destroy',
	]);


	Route::controller('dsk_platform_order', 'PlatformOrderController', [
		'getIndex'           => 'dsk_platform_order.index',
		'getCreate'          => 'dsk_platform_order.create',
		'getEdit'            => 'dsk_platform_order.edit',
		'postUpdate'         => 'dsk_platform_order.update',
		'postColor'          => 'dsk_platform_order.color',
		'postProgress'       => 'dsk_platform_order.progress',
		'getPicture'         => 'dsk_platform_order.picture',
		'getDetail'          => 'dsk_platform_order.detail',
		'postOverPicture'    => 'dsk_platform_order.over_picture',
		'postKfAgree'        => 'dsk_platform_order.kf_agree',
		'getKfHandle'        => 'dsk_platform_order.kf_handle',
		'postDestroy'        => 'dsk_platform_order.destroy',
		'postImport'         => 'dsk_platform_order.import',
		'getBatchPublish'    => 'dsk_platform_order.batch_publish',
		'postReload'         => 'dsk_platform_order.reload',
		'getGetIn'           => 'dsk_platform_order.get_in',
		'postRefund'         => 'dsk_platform_order.refund',
		'postAssignPublish'  => 'dsk_platform_order.assign_publish',
		'getQuestion'        => 'dsk_platform_order.question',
		'getHandleQuestion'  => 'dsk_platform_order.handle_question',
		'postOrderRePublish' => 'dsk_platform_order.order_re_publish',
		'postRePublish'      => 'dsk_platform_order.re_publish',
		'postNote'           => 'dsk_platform_order.note',
		'getMoney'           => 'dsk_platform_order.money',
		'getSend_Message'    => 'dsk_platform_order.send_message',
		'getShowField'       => 'dsk_platform_order.show_field',
		'postChangePwd'      => 'dsk_platform_order.change_pwd',
		'getExportIndex'     => 'dsk_platform_order.export_index',
		'postDisableUrgency' => 'dsk_platform_order.disable_urgency',
		'postEnableUrgency'  => 'dsk_platform_order.enable_urgency',
		'postBatchRePublish' => 'dsk_platform_order.batch_re_publish',
		'postEnableRenew'    => 'dsk_platform_order.enable_renew',
		'postDisableRenew'   => 'dsk_platform_order.disable_renew',
	]);

	Route::controller('dsk_platform_statics', 'PlatformStaticsController', [
		'getMoney'   => 'dsk_platform_statics.money',
		'getJournal' => 'dsk_platform_statics.journal',
		'getEnter'   => 'dsk_platform_statics.enter',
		'getSms'     => 'dsk_platform_statics.sms',
	]);

	Route::controller('dsk_platform_sync_log', 'PlatformSyncLogController', [
		'getIndex' => 'dsk_platform_sync_log.index',
	]);

	Route::controller('dsk_platform_account', 'PlatformAccountController', [
		'getIndex'    => 'dsk_platform_account.index',
		'getCreate'   => 'dsk_platform_account.create',
		'getEdit'     => 'dsk_platform_account.edit',
		'getBind'     => 'dsk_platform_account.bind',
		'postDestroy' => 'dsk_platform_account.destroy',
	]);

	Route::controller('dsk_platform_status_mao', 'PlatformStatusMaoController', [
		'getShow'          => 'dsk_platform_status_mao.show',
		'postPublish'      => 'dsk_platform_status_mao.publish',
		'getDetail'        => 'dsk_platform_status_mao.detail',
		'postDelete'       => 'dsk_platform_status_mao.delete',
		'postProgress'     => 'dsk_platform_status_mao.progress',
		'getMessage'       => 'dsk_platform_status_mao.message',
		'postLock'         => 'dsk_platform_status_mao.lock',
		'postSpecial'      => 'dsk_platform_status_mao.special',
		'postStar'         => 'dsk_platform_status_mao.star',
		'postCancel'       => 'dsk_platform_status_mao.cancel',
		'postCancelCancel' => 'dsk_platform_status_mao.cancel_cancel',
		'postCancelAgree'  => 'dsk_platform_status_mao.cancel_agree',
		'postKf'           => 'dsk_platform_status_mao.kf',
		'getPicShow'       => 'dsk_platform_status_mao.pic_show',
		'getAllList'       => 'dsk_platform_status_mao.all_list',
		'postOver'         => 'dsk_platform_status_mao.over',
	]);
	Route::controller('dsk_platform_status_mama', 'PlatformStatusMamaController', [
		'getShow'          => 'dsk_platform_status_mama.show',
		'postPublish'      => 'dsk_platform_status_mama.publish',
		'getDetail'        => 'dsk_platform_status_mama.detail',
		'postDelete'       => 'dsk_platform_status_mama.delete',
		'postProgress'     => 'dsk_platform_status_mama.progress',
		'getMessage'       => 'dsk_platform_status_mama.message',
		'getMessageNext'   => 'dsk_platform_status_mama.message_next',
		'postLock'         => 'dsk_platform_status_mama.lock',
		'postSpecial'      => 'dsk_platform_status_mama.special',
		'postStar'         => 'dsk_platform_status_mama.star',
		'postCancel'       => 'dsk_platform_status_mama.cancel',
		'postCancelCancel' => 'dsk_platform_status_mama.cancel_cancel',
		'postCancelAgree'  => 'dsk_platform_status_mama.cancel_agree',
		'postKf'           => 'dsk_platform_status_mama.kf',
		'getPicShow'       => 'dsk_platform_status_mama.pic_show',
		'getAllList'       => 'dsk_platform_status_mama.all_list',
		'postOver'         => 'dsk_platform_status_mama.over',
	]);
	// yi
	Route::controller('dsk_platform_status_yi', 'PlatformStatusYiController', [
		'getShow'         => 'dsk_platform_status_yi.show',
		'getDetail'       => 'dsk_platform_status_yi.detail',
		'postPublish'     => 'dsk_platform_status_yi.publish',
		'postDelete'      => 'dsk_platform_status_yi.delete',
		'postOver'        => 'dsk_platform_status_yi.over',
		'postLock'        => 'dsk_platform_status_yi.lock',
		'postUnLock'      => 'dsk_platform_status_yi.un_lock',
		'postAddTime'     => 'dsk_platform_status_yi.add_time',
		'postAddMoney'    => 'dsk_platform_status_yi.add_money',
		'postGamePwd'     => 'dsk_platform_status_yi.game_pwd',
		'getProgress'     => 'dsk_platform_status_yi.progress',
		'getProgressItem' => 'dsk_platform_status_yi.progress_item',
		'getMessage'      => 'dsk_platform_status_yi.message',
		'postKf'          => 'dsk_platform_status_yi.kf',
		'postCancel'      => 'dsk_platform_status_yi.cancel',
		'postStar'        => 'dsk_platform_status_yi.star',
	]);

	// baozi
	Route::controller('dsk_platform_status_baozi', 'PlatformStatusBaoziController', [
		'getShow'         => 'dsk_platform_status_baozi.show',
		'getDetail'       => 'dsk_platform_status_baozi.detail',
		'postPublish'     => 'dsk_platform_status_baozi.publish',
		'postDelete'      => 'dsk_platform_status_baozi.delete',
		'postOver'        => 'dsk_platform_status_baozi.over',
		'postLock'        => 'dsk_platform_status_baozi.lock',
		'postUnLock'      => 'dsk_platform_status_baozi.un_lock',
		'postAddTime'     => 'dsk_platform_status_baozi.add_time',
		'postAddMoney'    => 'dsk_platform_status_baozi.add_money',
		'postGamePwd'     => 'dsk_platform_status_baozi.game_pwd',
		'getProgress'     => 'dsk_platform_status_baozi.progress',
		'getProgressItem' => 'dsk_platform_status_baozi.progress_item',
		'getMessage'      => 'dsk_platform_status_baozi.message',
		'postKf'          => 'dsk_platform_status_baozi.kf',
		'postCancel'      => 'dsk_platform_status_baozi.cancel',
		'postStar'        => 'dsk_platform_status_baozi.star',
	]);

	//17代练
	Route::controller('dsk_platform_status_yq', 'PlatformStatusYqController', [
		'getShow'         => 'dsk_platform_status_yq.show',
		'getDetail'       => 'dsk_platform_status_yq.detail',
		'postPublish'     => 'dsk_platform_status_yq.publish',
		'postDelete'      => 'dsk_platform_status_yq.delete',
		'postOver'        => 'dsk_platform_status_yq.over',
		'postLock'        => 'dsk_platform_status_yq.lock',
		'postUnLock'      => 'dsk_platform_status_yq.un_lock',
		'postAddTime'     => 'dsk_platform_status_yq.add_time',
		'postAddMoney'    => 'dsk_platform_status_yq.add_money',
		'postSpecial'     => 'dsk_platform_status_yq.special',
		'postGamePwd'     => 'dsk_platform_status_yq.game_pwd',
		'getProgress'     => 'dsk_platform_status_yq.progress',
		'getProgressItem' => 'dsk_platform_status_yq.progress_item',
		'getMessage'      => 'dsk_platform_status_yq.message',
		'postKf'          => 'dsk_platform_status_yq.kf',
		'postCancel'      => 'dsk_platform_status_yq.cancel',
		'postStar'        => 'dsk_platform_status_yq.star',
	]);

	// 代练通状态
	Route::controller('dsk_platform_status_tong', 'PlatformStatusTongController', [
		'getShow'      => 'dsk_platform_status_tong.show',
		'postPublish'  => 'dsk_platform_status_tong.publish',
		'postDelete'   => 'dsk_platform_status_tong.delete',
		'getDetail'    => 'dsk_platform_status_tong.detail',
		'postLock'     => 'dsk_platform_status_tong.lock',
		'postSpecial'  => 'dsk_platform_status_tong.special',
		'getMessage'   => 'dsk_platform_status_tong.message',
		'postProgress' => 'dsk_platform_status_tong.progress',
		'postCancel'   => 'dsk_platform_status_tong.cancel',
		'postKf'       => 'dsk_platform_status_tong.kf',
		'postOver'     => 'dsk_platform_status_tong.over',
		'postStar'     => 'dsk_platform_status_tong.star',
	]);

	Route::controller('dsk_platform_status_employee', 'PlatformStatusEmployeeController', [
		'getMessage'       => 'dsk_platform_status_employee.message',
		'getShow'          => 'dsk_platform_status_employee.show',
		'getDetail'        => 'dsk_platform_status_employee.detail',
		'getProgress'      => 'dsk_platform_status_employee.progress',
		'postGamePwd'      => 'dsk_platform_status_employee.game_pwd',
		'postCancel'       => 'dsk_platform_status_employee.cancel',
		'postCancelCancel' => 'dsk_platform_employee.cancel_cancel',
	]);


	// 角色管理
	Route::controller('dsk_pam_role', 'PamRoleController', [
		'getIndex'    => 'dsk_pam_role.index',
		'getCreate'   => 'dsk_pam_role.create',
		'postCheck'   => 'dsk_pam_role.check',
		'getMenu'     => 'dsk_pam_role.menu',
		'getEdit'     => 'dsk_pam_role.edit',
		'postDestroy' => 'dsk_pam_role.destroy',
	]);

	// 账户列表
	Route::controller('dsk_pam_account', 'PamAccountController', [
		'postStatus'  => 'dsk_pam_account.status',
		'getLog'      => 'dsk_pam_account.log',
		'getEdit'     => 'dsk_pam_account.edit',
		'postDestroy' => 'dsk_pam_account.destroy',
		'getCreate'   => 'dsk_pam_account.create',
		'getIndex'    => 'dsk_pam_account.index',
		'postDisable' => 'dsk_pam_account.disable',
		'postEnable'  => 'dsk_pam_account.enable',
		'getAcl'      => 'dsk_pam_account.acl',
	]);


	// ip 管理
	Route::controller('dsk_plugin_ip', 'PluginIpController', [
		'getIndex'    => 'dsk_plugin_ip.index',
		'getCreate'   => 'dsk_plugin_ip.create',
		'postDestroy' => 'dsk_plugin_ip.destroy',
		'postIp'      => 'dsk_plugin_ip.ip',
		'postNote'    => 'dsk_plugin_ip.note',
		'postCheck'   => 'dsk_plugin_ip.check',
	]);

	// 员工专属
	Route::controller('dsk_platform_employee', 'PlatformEmployeeController', [
		'getIndex'               => 'dsk_platform_employee.index',
		'getMoney'               => 'dsk_platform_employee.money',
		'getMoneyList'           => 'dsk_platform_employee.money_list',
		'getEmployeeOrderDetail' => 'dsk_platform_employee.employee_order_detail',
		'postDelete'             => 'dsk_platform_employee.delete',
		'postAssignEmployee'     => 'dsk_platform_employee.assign_employee',
		'getPublishToEmployee'   => 'dsk_platform_employee.publish_employee',
		'postHandle'             => 'dsk_platform_employee.handle',
		'getDetail'              => 'dsk_platform_employee.detail',
		'getProgress'            => 'dsk_platform_employee.progress',
		'getMessage'             => 'dsk_platform_employee.message',
		'getPicShow'             => 'dsk_platform_employee.pic_show',
		'postUpdateProgress'     => 'dsk_platform_employee.update_progress',
		'getUpdateProgress'      => 'dsk_platform_employee.update_progress',
		'getException'           => 'dsk_platform_employee.exception',
		'getCancelException'     => 'dsk_platform_employee.cancel_exception',
		'getExamine'             => 'dsk_platform_employee.examine',
		'postConfirmOrderOver'   => 'dsk_platform_employee.confirm_order_over',
		'postHandleCancel'       => 'dsk_platform_employee.handel_cancel',
		'getAssignPc'            => 'dsk_platform_employee.assign_pc',
		'postBranchHandle'       => 'dsk_platform_employee.branch_handle',
	]);

	Route::controller('dsk_platform_repeat', 'PlatformRepeatController', [
		'getIndex' => 'dsk_platform_repeat.index',
	]);

	// 发单价格管理
	Route::controller('dsk_billing_price', 'BillingPrice', [
		'getIndex' => 'dsk_billing_price.index',
	]);
	*/
});
<?php namespace System\Events;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;

/**
 * 事件监听
 * 这里不要使用 Class::class 这种形式, 无法判定是否重复KEY
 */
class ListenerServiceProvider extends EventServiceProvider
{

	/**
	 * The event handler mappings for the application.
	 * @var array
	 */
	protected $listen = [
		/* 权限操作
		 -------------------------------------------- */
		'exception.beforeRender' => [
			// 'System\Events\Listeners\Exception\BeforeRenderListener',
		],
	];

}

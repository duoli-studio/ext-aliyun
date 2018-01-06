<?php namespace System\Module\Listeners;


use Poppy\Framework\Application\Event;
use System\Classes\Traits\SystemTrait;

class ClearCacheListener
{

	use SystemTrait;

	/**
	 * @param Event $event
	 */
	public function handle($event)
	{
		collect([
			'modules',
			'modules.assets',
			'modules.menu',
			'modules.page',
			'modules.setting',
			'modules.develop_menus',
			'modules.backend_menus',
		])->each(function ($item) {
			$this->getCache()->forget($item);
		});
	}
}


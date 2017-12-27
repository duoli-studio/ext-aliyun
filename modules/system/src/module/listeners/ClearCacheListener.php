<?php namespace System\Module\Listeners;


use Poppy\Framework\Poppy\Events\PoppyOptimized;
use System\Classes\Traits\SystemTrait;

class ClearCacheListener
{

	use SystemTrait;

	/**
	 * @param PoppyOptimized $event
	 */
	public function handle(PoppyOptimized $event)
	{
		collect([
			'modules',
			'modules.assets',
			'modules.menu',
			'modules.page',
			'modules.setting',
		])->each(function ($item) {
			$this->getCache()->forget($item);
		});
	}
}


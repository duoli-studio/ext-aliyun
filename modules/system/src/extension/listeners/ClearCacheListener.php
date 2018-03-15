<?php namespace System\Extension\Listeners;

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
			'extensions',
		])->each(function ($item) {
			$this->getCache()->forget($item);
		});
	}
}


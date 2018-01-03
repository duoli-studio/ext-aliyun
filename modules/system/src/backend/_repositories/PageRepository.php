<?php namespace System\Backend\_repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class PageRepository.
 */
class PageRepository extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param Collection $collection
	 */
	public function initialize(Collection $collection)
	{
		$this->getModule()->pages()->each(function ($definition) {
			$this->items[] = $definition;
		});
	}
}

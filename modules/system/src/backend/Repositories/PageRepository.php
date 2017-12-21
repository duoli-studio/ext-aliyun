<?php namespace System\Backend\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Abstracts\CollectRepository;

/**
 * Class PageRepository.
 */
class PageRepository extends CollectRepository
{
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

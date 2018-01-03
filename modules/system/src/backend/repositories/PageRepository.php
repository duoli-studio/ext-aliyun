<?php namespace System\Backend\Repositories;

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

			// submit address
			if (
				isset($definition['tabs']) &&
				isset($definition['tabs']['configuration']) &&
				isset($definition['tabs']['submit'])
			) {
				$definition['tabs']['submit'] = url($definition['tabs']['submit']);
			}
			$this->items[] = $definition;
		});
	}
}

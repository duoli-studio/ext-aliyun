<?php namespace System\Permission\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * Class PermissionRepository.
 */
class PermissionRepository extends Repository
{
	/**
	 * Initialize.
	 *
	 * @param \Illuminate\Support\Collection $collection
	 */
	public function initialize(Collection $collection)
	{
		$collection->each(function ($definition, $identification) {
			$this->items[$identification] = $definition;
		});
	}
}

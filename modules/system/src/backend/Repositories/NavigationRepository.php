<?php namespace System\Backend\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class NavigationRepository.
 */
class NavigationRepository extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param Collection $data
	 */
	public function initialize(Collection $data)
	{
		$cache = $this->getCache();
		if ($cache instanceof TaggableStore) {
			$cache->tags('poppy');
		}

		$this->items = $cache->rememberForever('backend.navigation.repository', function () use ($data) {
			return $data->all();
		});
	}
}

<?php namespace Poppy\Framework\Addon\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Abstracts\CollectRepository;

/**
 * Class NavigationRepository.
 */
class NavigationRepository extends CollectRepository
{
	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{

		$this->items = $this->cache->tags('notadd')->rememberForever('addon.navigation.repository', function () use ($data) {
			$collection = collect();
			$data->each(function ($definition, $key) use ($collection) {
				if (is_array($definition) && $definition) {
					foreach ($definition as $item) {
						$collection->push($item);
					}
				}
			});

			return $collection->all();
		});
	}
}

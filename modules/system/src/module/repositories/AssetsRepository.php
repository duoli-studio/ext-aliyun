<?php namespace System\Module\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * Class AssetsRepository.
 */
class AssetsRepository extends Repository
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{
		$cache = $this->getCache();
		if ($cache instanceof TaggableStore) {
			$cache->tags('poppy');
		}
		$this->items = $cache->rememberForever('module.assets.repository', function () use ($data) {
			$collection = collect();
			$data->each(function ($items, $module) use ($collection) {
				$items = collect($items);
				$items->count() && $items->each(function ($items, $entry) use ($collection, $module) {
					$items = collect((array) $items);
					$items->count() && $items->each(function ($definition, $identification) use ($collection, $entry, $module) {
						$data = [
							'entry'          => $entry,
							'for'            => 'module',
							'identification' => $identification,
							'module'         => $module,
							'permission'     => data_get($definition, 'permission', ''),
						];
						collect((array) data_get($definition, 'scripts', []))->each(function ($path) use ($collection, $data) {
							$collection->push(array_merge($data, [
								'file' => $path,
								'type' => 'script',
							]));
						});
						collect((array) data_get($definition, 'stylesheets', []))->each(function ($path) use ($collection, $data) {
							$collection->push(array_merge($data, [
								'file' => $path,
								'type' => 'stylesheet',
							]));
						});
					});
				});
			});

			return $collection->all();
		});
	}
}

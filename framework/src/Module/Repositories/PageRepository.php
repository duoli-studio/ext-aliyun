<?php namespace Poppy\Framework\Module\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Abstracts\CollectRepository;

/**
 * Class PageRepository.
 */
class PageRepository extends CollectRepository
{
	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $slugs
	 */
	public function initialize(Collection $slugs)
	{
		$cache = $this->getCache();
		if ($cache instanceof TaggableStore) {
			$cache->tags('poppy');
		}
		$this->items = $cache->rememberForever('module.page.repository', function () use ($slugs) {
			$collection = collect();
			$slugs->each(function ($items, $module) use ($collection) {
				collect($items)->each(function ($definition, $identification) use ($collection, $module) {
					$key = $module . '/' . $identification;
					$collection->put($key, $definition);
				});
			});
			$collection->transform(function ($definition) {
				data_set($definition, 'tabs', collect($definition['tabs'])->map(function ($definition) {
					data_set($definition, 'fields', collect($definition['fields'])->map(function ($definition) {
						$setting = $this->getConf()->get($definition['key'], '');
						if (isset($definition['format'])) {
							switch ($definition['format']) {
								case 'boolean':
									$definition['value'] = boolval($setting);
									break;
								default:
									$definition['value'] = $setting;
									break;
							}
						}
						else {
							$definition['value'] = $setting;
						}

						return $definition;
					}));

					return $definition;
				}));

				return $definition;
			});

			return $collection->all();
		});
	}
}

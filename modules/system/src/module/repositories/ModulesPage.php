<?php namespace System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class PageRepository.
 */
class ModulesPage extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param Collection $slugs
	 */
	public function initialize(Collection $slugs)
	{
		$this->items = $this->getCache('poppy')->rememberForever(
			'modules.page',
			function () use ($slugs) {
			$collection = collect();
			$slugs->each(function ($items, $module) use ($collection) {
				collect($items)->each(function ($definition, $identification) use ($collection, $module) {
					$key = $module . '/' . $identification;
					$collection->put($key, $definition);
				});
			});
			$collection->transform(function ($definition) {
				data_set($definition, 'tabs', collect($definition['tabs'])->map(function ($definition) {
					$definition['submit'] = url($definition['submit']);
					data_set($definition, 'fields', collect($definition['fields'])->map(function ($definition) {
						$setting = $this->getSetting()->get($definition['key'], '');
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
		}
		);
	}
}

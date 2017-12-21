<?php namespace Poppy\Framework\Module\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Abstracts\CollectRepository;
use System\Classes\Traits\SystemTrait;

/**
 * Class MenuRepository.
 */
class MenuRepository extends CollectRepository
{
	use SystemTrait;

	/**
	 * @var array
	 */
	protected $configuration = [];

	/**
	 * @var \Illuminate\Support\Collection
	 */
	protected $structures;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $slugs
	 */
	public function initialize(Collection $slugs)
	{
		$configuration       = json_decode($this->getSetting()->get('administration.menus', ''), true);
		$this->configuration = is_array($configuration) ? $configuration : [];
		$cache               = $this->getCache();
		if ($cache instanceof TaggableStore) {
			$cache->tags('poppy');
		}
		$this->items = $cache->rememberForever('module.menu.repository', function () use ($slugs) {
			$collection = collect();
			$slugs      = $slugs->map(function ($definition, $key) {
				if ($key == 'system') {
					return collect($definition)->map(function ($definition, $key) {
						if ($key == 'global') {
							return collect($definition)->map(function ($definition, $key) {
								if ($key == 'children') {
									return collect($definition)->map(function ($definition) {
										if (isset($definition['injection'])) {
											$children = isset($definition['children']) ? collect((array) $definition['children']) : collect();
											switch ($definition['injection']) {
												case 'addon':
													$this->getPlugin()->navigations()->each(function ($definition) use ($children) {
														$children->push([
															'path' => $definition['path'] ?? '/',
															'text' => $definition['text'] ?? '未定义',
														]);
													});
													break;
												case 'global':
													$this->getBackend()->pages()->each(function ($definition) use ($children) {
														if ($definition['initialization']['target'] == 'global') {
															$children->push([
																'path' => $definition['initialization']['path'],
																'text' => $definition['initialization']['name'],
															]);
														}
													});
													break;
											}
											$definition['children'] = $children->toArray();

											return $definition;
										}
										else {
											return $definition;
										}
									})->toArray();
								}
								else {
									return $definition;
								}
							})->toArray();
						}
						else {
							return $definition;
						}
					})->toArray();
				}
				else {
					return $definition;
				}
			});
			$slugs->each(function ($definition, $module) use ($collection) {
				$this->parse($definition, $module, $collection);
			});

			return $collection->all();
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function structures()
	{
		if ($this->structures == null) {
			$collection = collect();
			collect($this->items)->each(function ($definition, $index) use ($collection) {
				if (!$this->has($definition['parent']) && $this->getModule()->repository()->has($definition['parent'])) {
					$this->structure($index, $collection);
				}
			});
			$this->structures = $collection->sortBy('order');
		}

		return $this->structures;
	}

	/**
	 * @param                                $index
	 * @param \Illuminate\Support\Collection $collection
	 */
	protected function structure($index, Collection $collection)
	{
		$children = collect();
		collect($this->items)->filter(function ($item) use ($index) {
			return $item['parent'] == $index;
		})->each(function ($definition, $index) use ($children) {
			$this->structure($index, $children);
		});
		$definition             = $this->items[$index];
		$definition['children'] = $children->sortBy('order')->toArray();
		$definition['index']    = $index;
		$collection->put($index, $definition);
	}

	/**
	 * @param array                          $items
	 * @param string                         $prefix
	 * @param \Illuminate\Support\Collection $collection
	 */
	private function parse(array $items, string $prefix, Collection $collection)
	{
		collect($items)->each(function ($definition, $key) use ($collection, $prefix) {
			$key = $prefix . '/' . $key;
			if (isset($this->configuration[$key])) {
				$definition['enabled'] = isset($this->configuration[$key]['enabled']) ? boolval($this->configuration[$key]['enabled']) : false;
				$definition['order']   = isset($this->configuration[$key]['order']) ? intval($this->configuration[$key]['order']) : 0;
				$definition['text']    = $this->configuration[$key]['text'] ?? $definition['text'];
			}
			else {
				$definition['enabled'] = true;
				$definition['order']   = 0;
			}
			$definition['parent'] = $prefix;
			if (isset($definition['children'])) {
				$this->parse($definition['children'], $key, $collection);
				unset($definition['children']);
			}
			$collection->put($key, $definition);
		});
	}
}

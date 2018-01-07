<?php namespace System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Support\Abstracts\Repository;

class ModulesBackendMenu extends Repository
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{
		$this->items = $this->getCache('poppy')->rememberForever(
			'modules.backend_menus', function () use ($data) {
			$collection = collect();
			$data->each(function ($items, $slug) use ($collection) {
				$items = collect($items);
				$items->count() && $items->each(function ($item, $entry) use ($collection, $slug, $items) {
					$items[$entry] = $this->handleNavigation($item);
					$collection->put($slug, $items);
				});
			});
			return $collection->all();
		});
	}


	private function handleNavigation($definition)
	{
		$childrenRoutes = [];
		if (isset($definition['children']) && is_array($definition['children'])) {
			foreach ($definition['children'] as $key => $define) {
				if (isset($define['route'])) {
					$childrenRoutes[] = $define['route'];
				}
				$definition['children'][$key] = $this->handleNavigation($define);
			}
		}
		$route                = $definition['route'] ?? '';
		$route_params         = $definition['route_param'] ?? '';
		$param                = $definition['param'] ?? '';
		$url                  = $route ? route_url($route, $route_params, $param) : '#';
		$definition['url']    = $url;
		$definition['routes'] = $childrenRoutes;
		unset($definition['param'], $definition['route_param']);
		$definition['key'] = UtilHelper::md5($definition);
		return $definition;
	}

}

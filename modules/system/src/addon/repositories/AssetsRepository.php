<?php namespace System\Addon\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class AssetsRepository.
 */
class AssetsRepository extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{
		$this->items = $this->getCache('poppy')->rememberForever(
			'addon.assets.repository', function () use ($data) {
			$collection = collect();
			$data->each(function ($items, $module) use ($collection) {
				$items = collect($items);
				$items->count() && $items->each(function ($items, $entry) use ($collection, $module) {
					$items = collect($items);
					$items->count() && $items->each(function ($definition, $identification) use ($collection, $entry, $module) {
						$data = [
							'entry'          => $entry,
							'for'            => 'addon',
							'identification' => $identification,
							'module'         => $module,
							'permission'     => data_get($definition, 'permission', ''),
						];
						collect((array) data_get($definition, 'scripts'))->each(function ($path) use ($collection, $data) {
							$collection->push(array_merge($data, [
								'file' => $path,
								'type' => 'script',
							]));
						});
						collect((array) data_get($definition, 'stylesheets'))->each(function ($path) use ($collection, $data) {
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

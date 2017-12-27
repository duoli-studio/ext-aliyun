<?php namespace System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * Class AssetsRepository.
 */
class ModulesSetting extends Repository
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{
		$this->items = $this->getCache('poppy')->rememberForever(
			'modules.setting', function () use ($data) {
			$collection = collect();
			$data->each(function ($items, $slug) use ($collection) {
				$items = collect($items);
				$items->count() && $items->each(function ($items, $entry) use ($collection, $slug) {
					$collection->put($entry, $items);
				});
			});
			return $collection->all();
		});
	}
}

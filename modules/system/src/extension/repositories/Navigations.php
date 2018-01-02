<?php namespace System\Extension\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class NavigationRepository.
 */
class Navigations extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{

		$this->items = $this->getCache('poppy')->rememberForever(
			'extensions.navigation', function () use ($data) {
			$collection = collect();
			$data->each(function ($definition, $key) use ($collection) {
				if (is_array($definition) && $definition) {
					foreach ($definition as $item) {
						$collection->push($item);
					}
				}
			});

			return $collection->all();
		}
		);
	}
}

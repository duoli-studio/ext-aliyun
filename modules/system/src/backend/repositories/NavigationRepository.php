<?php namespace System\Backend\Repositories;

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
		$this->items = $this->getCache('poppy')->rememberForever(
			'backend.navigation',
			function () use ($data) {
				return $data->all();
			}
		);
	}
}

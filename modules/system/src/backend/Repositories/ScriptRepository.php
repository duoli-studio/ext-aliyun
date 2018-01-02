<?php namespace System\Backend\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class ScriptRepository.
 */
class ScriptRepository extends Repository
{
	use SystemTrait;

	/**
	 * Initialize.
	 * @param Collection $collection
	 */
	public function initialize(Collection $collection)
	{
		$this->getModule()->assets()->filter(function ($definition) {
			return isset($definition['entry'])
				&& isset($definition['type'])
				&& $definition['entry'] == 'administration'
				&& $definition['type'] == 'script';
		})->each(function ($definition) {
			$definition['file'] = $this->getUrl()->asset($definition['file']);
			$this->items[]      = $definition;
		});
	}
}

<?php namespace Poppy\Framework\Classes\Abstracts;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;

/**
 * Class Repository.
 */
abstract class CollectRepository extends Collection
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param Collection $collection
	 */
	abstract public function initialize(Collection $collection);
}

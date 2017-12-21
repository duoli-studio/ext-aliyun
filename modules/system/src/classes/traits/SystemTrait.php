<?php namespace System\Classes\Traits;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Backend\BackendManager;

/**
 * Class Helpers.
 */
trait SystemTrait
{
	use PoppyTrait;

	/**
	 * @return BackendManager
	 */
	protected function getBackend(): BackendManager
	{
		return $this->getContainer()->make('backend');
	}
}
<?php namespace System\Classes\Traits;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Backend\BackendManager;
use System\Setting\Repository\SettingRepository;

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

	/**
	 * @return SettingRepository
	 */
	protected function getSetting(): SettingRepository
	{
		return $this->getContainer()->make('system.conf');
	}
}
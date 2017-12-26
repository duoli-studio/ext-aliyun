<?php namespace System\Classes\Traits;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Addon\AddonManager;
use System\Backend\BackendManager;
use System\Extension\ExtensionManager;
use System\Module\ModuleManager;
use System\Permission\PermissionManager;
use System\Setting\Repository\SettingRepository;
use Tymon\JWTAuth\JWTAuth;

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
	 * @return PermissionManager
	 */
	protected function getPermission(): PermissionManager
	{
		return $this->getContainer()->make('permission');
	}

	/**
	 * @return SettingRepository
	 */
	protected function getSetting(): SettingRepository
	{
		return $this->getContainer()->make('setting');
	}

	/**
	 * @return JWTAuth
	 */
	protected function getJwt(): JWTAuth
	{
		return $this->getContainer()->make('tymon.jwt.auth');
	}

	/**
	 * Get mailer instance.
	 * @return ModuleManager
	 */
	protected function getModule(): ModuleManager
	{
		return $this->getContainer()->make('module');
	}


	/**
	 * @return ExtensionManager
	 */
	protected function getExtension(): ExtensionManager
	{
		return $this->getContainer()->make('extension');
	}

	/**
	 * @return AddonManager
	 */
	protected function getAddon(): AddonManager
	{
		return $this->getContainer()->make('addon');
	}
}
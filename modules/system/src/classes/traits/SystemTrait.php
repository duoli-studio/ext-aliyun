<?php namespace System\Classes\Traits;

use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Addon\AddonManager;
use System\Backend\BackendManager;
use System\Extension\ExtensionManager;
use System\Models\PamAccount;
use System\Module\ModuleManager;
use System\Permission\PermissionManager;
use System\Setting\Repository\SettingRepository;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class Helpers.
 */
trait SystemTrait
{
	use PoppyTrait, AppTrait;

	/**
	 * @var PamAccount;
	 */
	protected $bePam;

	/**
	 * @var PamAccount
	 */
	protected $webPam;

	/**
	 * Check Backend User login and permission.
	 * @param string $permission
	 * @return bool
	 */
	public function checkBe($permission = '')
	{
		$this->bePam = $this->getAuth()->guard(PamAccount::GUARD_BACKEND)->user();
		if (!$this->bePam) {
			return $this->setError(trans('system::act.check_be_need_login'));
		}

		// check permission
		if ($permission && !$this->bePam->capable($permission)) {
			return $this->setError(trans('system::act.check_permission_failed'));
		}
		return true;
	}


	/**
	 * Check Web User Permission
	 * @param string $permission
	 * @return bool
	 */
	public function checkWeb($permission = '')
	{
		$this->webPam = $this->getAuth()->guard(PamAccount::GUARD_WEB)->user();
		if (!$this->webPam) {
			return $this->setError(trans('system::act.check_web_need_login'));
		}
		if ($permission && !$this->bePam->capable($permission)) {
			return $this->setError(trans('system::act.check_permission_failed'));
		}
		return true;
	}


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
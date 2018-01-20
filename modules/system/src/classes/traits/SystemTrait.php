<?php namespace System\Classes\Traits;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Classes\Traits\PoppyTrait;
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
	protected $pam;

	/**
	 * @return PamAccount
	 */
	public function getPam(): PamAccount
	{
		return $this->pam;
	}

	/**
	 * Check Set Password
	 * @return bool
	 */
	public function existsPassword()
	{
		if (!$this->pam->password) {
			return $this->setError('密码为空');
		}
		return true;
	}

	/**
	 * 检查 pam 用户
	 * @return bool
	 */
	public function checkPam()
	{
		if (!$this->pam) {
			return $this->setError(trans('system::act.check_permission_need_login'));
		}
		return true;
	}

	/**
	 * Check Backend User login and permission.
	 * @param string $permission
	 * @return bool
	 */
	public function checkPermission($permission = '')
	{
		if (!$this->pam) {
			return $this->setError(trans('system::act.check_permission_need_login'));
		}

		// check permission
		if ($permission && !$this->pam->capable($permission)) {
			return $this->setError(trans('system::act.check_permission_failed'));
		}
		return true;
	}


	/**
	 * check if is web user
	 * @return bool
	 */
	public function isJwtUser()
	{
		$guard = $this->getJwtWebGuard();
		if ($guard->guest()) {
			return false;
		}
		elseif ($guard->user()->type != PamAccount::TYPE_USER) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * check if is backend user
	 * @return bool
	 */
	public function isJwtBackend()
	{
		$guard = $this->getJwtBeGuard();
		if ($guard->guest()) {
			return false;
		}
		elseif ($guard->user()->type != PamAccount::TYPE_BACKEND) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Set Pam Account.
	 * @param $pam
	 * @return $this
	 */
	public function setPam($pam)
	{
		$this->pam = $pam;
		return $this;
	}


	/**
	 * Get Jwt Backend Guard
	 */
	public function getJwtBeGuard()
	{
		return $this->getAuth()->guard(PamAccount::GUARD_JWT_BACKEND);
	}

	/**
	 * @return Guard|StatefulGuard
	 */
	public function getBeGuard()
	{
		return $this->getAuth()->guard(PamAccount::GUARD_BACKEND);
	}


	public function getJwtWebGuard()
	{
		return $this->getAuth()->guard(PamAccount::GUARD_JWT_WEB);
	}


	public function getWebGuard()
	{
		return $this->getAuth()->guard(PamAccount::GUARD_WEB);
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
}
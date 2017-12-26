<?php namespace System\Classes\Traits;

use Poppy\Framework\Classes\Traits\AppTrait;
use System\Models\PamAccount;

/**
 * Class Helpers.
 */
trait SystemAppTrait
{
	use AppTrait, SystemTrait;

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
}
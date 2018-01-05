<?php namespace System\Classes\Traits;

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
trait BackendTrait
{

	public function backendShare()
	{
		\View::share([
			'_pam' => \Auth::guard(PamAccount::GUARD_BACKEND)->user(),
		]);
	}
}
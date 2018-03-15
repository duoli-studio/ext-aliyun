<?php namespace System\Classes\Traits;

use System\Models\PamAccount;

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
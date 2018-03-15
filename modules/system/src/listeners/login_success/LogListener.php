<?php namespace System\Listeners\LoginSuccess;

use Carbon\Carbon;
use Poppy\Framework\Helper\EnvHelper;
use System\Event\LoginSuccessEvent;
use System\Models\PamLog;

class LogListener
{
	/**
	 * @param LoginSuccessEvent $event
	 * @throws \Exception
	 */
	public function handle(LoginSuccessEvent $event)
	{
		$pam = $event->pam();
		// 后台授权登录不计入日志
		if (\Session::has('desktop_auth')) {
			return;
		}

		PamLog::create([
			'account_id'   => $pam->id,
			'account_type' => $pam->type,
			'type'         => 'success',
			'ip'           => EnvHelper::ip(),
			'area_text'    => '',
			'area_name'    => '',
		]);

		// 删除 60 天以外的登录日志
		PamLog::where('created_at', '<', Carbon::now()->subDays(60))->delete();
	}
}


<?php namespace User\Events\Listeners;


use Poppy\Framework\Helper\EnvHelper;
use Illuminate\Auth\Events\Login;
use User\Models\PamAccount;
use User\Models\PamLog;


class LoginLog
{

	/**
	 * Handle the event.
	 * @param Login $event
	 */
	public function handle($event)
	{
		// 后台授权登录不计入日志
		if (\Session::has('desktop_auth')) {
			return;
		}
		/** @var PamAccount $user */
		$user     = $event->user;
		$ip       = EnvHelper::ip();
		$areaName = '';
		PamLog::create([
			'account_id'    => $user->id,
			'ip'            => $ip,
			'log_area_text' => '',
			'log_area_name' => $areaName,
			'log_area_id'   => '',
			'log_content'   => '登陆成功',
		]);
	}

}


<?php namespace System\Listeners;

use Poppy\Framework\Helper\EnvHelper;
use System\Models\PamLog;

class LoginIpBannedLog
{
	/**
	 * Create the event handler.
	 */
	public function __construct()
	{
	}

	/**
	 * @param $user
	 */
	public function handle($user)
	{
		PamLog::create([
			'account_id'   => $user->account_id,
			'account_name' => $user->account_name,
			'account_type' => $user->account_type,
			'log_type'     => 'error',
			'log_ip'       => EnvHelper::ip(),
			'log_content'  => 'ip 禁止登陆',
		]);
	}
}


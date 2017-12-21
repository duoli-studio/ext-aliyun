<?php namespace User\Events\Listeners;


use Poppy\Framework\Helper\EnvHelper;
use User\Models\PamAccount;
use User\Models\PamLog;

/**
 * 失败日志
 */
class FailedLog
{

	/**
	 * Create the event handler.
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 * @return void
	 */
	public function handle($credentials)
	{
		$account      = PamAccount::getByAccountName($credentials['account_name']);
		$account_id   = '';
		$account_name = $credentials['account_name'];
		$account_type = '';
		if ($account) {
			$account_id   = $account['account_id'];
			$account_type = $account['account_type'];
		}
		$content = '尝试登陆失败, 用户信息不匹配';
		if ($account_type != $credentials['account_type']) {
			$content = '范围[' . $account_type . ']用户跨域登陆, 登录失败';
		}
		PamLog::create([
			'account_id'   => $account_id,
			'account_name' => $account_name,
			'account_type' => $account_type,
			'log_type'     => 'error',
			'log_ip'       => EnvHelper::ip(),
			'log_content'  => $content,
		]);
	}

}



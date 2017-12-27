<?php namespace Util\Util\Console;

use Illuminate\Console\Command;

/**
 * 获取代练妈妈列表
 */
class ClearCaptchaCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'util:clear-captcha';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Clear Disabled Captcha.';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		// todo :  clear disabled captcha
		\Log::debug('clear disabled captcha');
	}
}

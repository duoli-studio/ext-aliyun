<?php namespace System\Commands;

use Illuminate\Console\Command;

/**
 * 清除失效验证码
 */
class CaptchaClearCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'system:captcha-clear';

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
		// todo @ 张年文 clear disabled captcha
		\Log::debug('clear disabled captcha');
	}
}
<?php namespace System\Commands;

use Illuminate\Console\Command;
use Poppy\Framework\Agamotto\Agamotto;
use System\Action\Pam;

class PamAutoEnableCommand extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'system:pam-auto_enable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Enable Pam Item;';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// 定时自动启用禁用用户
		(new Pam())->autoEnable();
		$this->info(Agamotto::now() . '[Console:' . $this->signature . ']');
	}
}
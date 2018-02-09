<?php namespace System\Commands;


use Illuminate\Console\Command;


class LogCommand extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'system:log';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'Tail today log.';


	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$this->info('Please Run Command:' .
			"\n" . 'tail -20f storage/logs/laravel-`date +%F`.log' .
			"\n" . 'tail -20f storage/logs/laravel-`date +%F`.log'
		);
	}
}

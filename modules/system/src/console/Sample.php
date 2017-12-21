<?php namespace System\Console;

use Illuminate\Console\Command;

class Sample extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'system:sample';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'system sample schedule';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$this->info('system sample output!');
	}
}
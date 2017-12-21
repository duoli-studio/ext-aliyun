<?php namespace Poppy\Extension\Worker\Commands;


use Illuminate\Console\Command;
use Workerman\Worker;

class ReloadCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:worker:reload';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reload workerman';

	/**
	 * Execute the console command.
	 */
	public function handle() {
		//
		global $argv;
		$argv[0] = 'artisan';
		$argv[1] = 'reload';

		Worker::runAll();
	}

}



<?php namespace Poppy\Extension\Worker\Commands;


use Illuminate\Console\Command;
use Workerman\Worker;

class StatusCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:worker:status';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'View workerman status';

	/**
	 * Execute the console command.
	 */
	public function handle() {
		//
		global $argv;
		$argv[0] = 'artisan';
		$argv[1] = 'status';

		Worker::runAll();
	}

}


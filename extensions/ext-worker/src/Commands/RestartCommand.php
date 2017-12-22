<?php namespace Poppy\Extension\Worker\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Workerman\Worker;

class RestartCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:worker:restart';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Restart workerman';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		//
		$deamonMode = $this->option('deamon');
		global $argv;
		$argv[0] = 'artisan';
		$argv[1] = 'restart';
		$argv[2] = $deamonMode ? '-d' : null;

		Worker::runAll();
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			['deamon', null, InputOption::VALUE_OPTIONAL, 'Deamon mode.', true],
		];
	}

}
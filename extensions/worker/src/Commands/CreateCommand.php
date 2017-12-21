<?php namespace Poppy\Extension\Worker\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:worker:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new workerman application';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		//
		$name       = $this->argument('name');
		$workerOnly = $this->option('worker-only');

		$workermanApps = app_path('Handlers/Workerman');
		if (!is_dir($workermanApps)) {
			mkdir($workermanApps, 0777, true);
		}
		$appPath = $workermanApps . DIRECTORY_SEPARATOR . $name;
		if (!is_dir($appPath)) {
			mkdir($appPath);
		}
		$appType = $workerOnly == 1 ? 'WorkerApplication' : 'GatewayBusinessWorkerApplication';
		$files   = [];
		foreach (['Event.php', 'start.php'] as $item) {
			$fileName         = $item;
			$files[$fileName] = [
				'source'      =>
					__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $appType . DIRECTORY_SEPARATOR . $fileName,
				'destination' => $appPath . DIRECTORY_SEPARATOR . $fileName,
			];
		}
		if ($workerOnly == 1) {
			unset($files['Event.php']);
		}
		foreach ($files as $file) {
			$content = file_get_contents($file['source']);
			$content = str_replace('{WorkermanAppName}', $name, $content);
			file_put_contents($file['destination'], $content);
		}

		$this->info('Workerman application "' . $name . '" created at ' . $appPath);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return [
			['name', InputArgument::REQUIRED, 'A workerman application needs a name.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			[
				'worker-only',
				null,
				InputOption::VALUE_NONE,
				'Create application in Worker mode.',
			],
		];
	}
}

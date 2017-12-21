<?php namespace Poppy\Extension\Worker\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Workerman\Worker;

class StartCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:worker:start';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start workerman';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		//
		$daemonMode = $this->option('daemon');

		// 检查扩展
		if (!extension_loaded('pcntl')) {
			$this->error("Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n");
			return;
		}

		if (!extension_loaded('posix')) {
			$this->error("Please install posix extension. See http://doc3.workerman.net/install/install.html\n");
			return;
		}

		// 标记是全局启动
		define('GLOBAL_START', 1);

		$applications = \Config::get('poppy-worker.applications');
		// 加载所有Applications/*/start.php，以便启动所有服务
		foreach ($applications as $application => $config) {
			$startPath = base_path($application);
			$this->info($startPath);
			if (is_file($startPath)) {
				require_once $startPath;
			}
		}
		// 运行所有服务

		global $argv;
		$argv[0] = 'artisan';
		$argv[1] = 'start';
		$argv[2] = $daemonMode ? '-d' : null;

		Worker::runAll();
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['daemon', null, InputOption::VALUE_OPTIONAL, 'Daemon mode.', true],
		];
	}

}


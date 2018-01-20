<?php namespace System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * 使用命令行生成 api 文档
 */
class DocCommand extends Command
{

	protected $signature = 'system:doc
		{type : action}
	';

	protected $description = 'Generate Doc Document';


	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$type = $this->argument('type');
		switch ($type) {
			case 'api':
				if (!command_exist('apidoc')) {
					$this->error("apidoc 命令不存在\n");
				}
				else {
					$dirs = [
						'system',
					];
					// 多少个任务
					$bar = $this->output->createProgressBar(count($dirs));

					foreach ($dirs as $dir) {
						$this->performTask($dir);
						// 一个任务处理完了，可以前进一点点了
						$bar->advance();
					}
					$bar->finish();
				}
				break;
			case 'php':
				$this->info('Please Run Command:'."\n".'php ./resources/sami/sami.phar update ./resources/sami/config.php');
				break;

		}

	}

	private function performTask($dir)
	{
		$lower   = strtolower($dir);
		$shell   = "apidoc -i " . base_path('modules/' . $dir . '/src/request/api') . '  -o ' . base_path('public/docs/' . $lower);
		$process = new Process($shell);
		$process->start();
		$process->wait(function ($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . "\n");
			}
		});
	}
}

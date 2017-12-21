<?php namespace Poppy\Extension\ApiDoc\Command;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * 使用命令行生成 api 文档
 */
class ApiDoc extends Command
{

	protected $signature = 'duoli:apidoc';

	protected $description = 'Generate Api Doc Document';


	/**
	 * Execute the console command.
	 */
	public function handle()
	{

		if (!command_exist('apidoc')) {
			$this->error("apidoc 命令不存在");
		}
		else {
			$catalog = config('duoli-api_doc.catalog');
			if (!$catalog) {
				$this->error("尚未配置 apidoc 生成目录");
				return;
			}
			// 多少个任务
			$bar = $this->output->createProgressBar(count($catalog));

			foreach ($catalog as $key => $dir) {
				if (isset($dir['origin']) && isset($dir['doc'])) {
					$this->performTask($key, $dir);
					// 一个任务处理完了，可以前进一点点了
					$bar->advance();
				}
				else {
					$this->error("尚未配置 {$key} 所对应的文档目录");
				}

			}
			$bar->finish();
		}
	}

	private function performTask($key, $dir)
	{
		$path = base_path($dir['origin']);
		$aim  = base_path($dir['doc']);
		if (!file_exists($path)) {
			$this->error('Err > 目录 `' . $path . '` 不存在');
			return;
		}
		$lower   = strtolower($key);
		$shell   = "apidoc -i " . $path . '  -o ' . $aim;
		$process = new Process($shell);
		$process->start();
		$process->wait(function ($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . "\n");
			}
		});
	}
}

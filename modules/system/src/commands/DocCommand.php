<?php namespace System\Commands;

use Illuminate\Console\Command;
use Poppy\Framework\Helper\FileHelper;
use Symfony\Component\Process\Process;
use System\Classes\Traits\SystemTrait;

/**
 * 使用命令行生成 api 文档
 */
class DocCommand extends Command
{
	use SystemTrait;

	protected $signature = 'system:doc
		{type : action}
	';

	protected $description = 'Generate Api Doc Document';

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
					$catalog = config('fe.apidoc');
					if (!$catalog) {
						$this->error('尚未配置 apidoc 生成目录');

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
				break;
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
		$filter = data_get($dir, 'filter');
		$f      = '';
		if (is_array($filter) && count($filter)) {
			foreach ($filter as $sf) {
				$f .= ' -f ' . $sf;
			}
		}
		$lower = strtolower($key);
		$shell = 'apidoc -i ' . $path . '  -o ' . $aim . ' ' . $f;
		$this->info($shell);
		$process = new Process($shell);
		$process->start();
		$process->wait(function ($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . "\n");
			}
		});
	}
}

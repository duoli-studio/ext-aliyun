<?php namespace Poppy\Extension\Fe\Command;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use System\Classes\Traits\SystemTrait;

/**
 * 使用命令行生成 api 文档
 */
class DocCommand extends Command
{
	use SystemTrait;

	protected $signature = 'ext:fe_doc
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
					$catalog = config('poppy.extension.fe.catalog');
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
				break;
			case 'php':
				$this->info('Please Run Command:' . "\n" . 'php ./extensions/ext-fe/resources/sami/sami.phar update ./extensions/ext-fe/resources/sami/config.php');
				break;
			case 'poppy':
				$this->getFile()->copyDirectory(base_path('extensions/ext-fe/resources/docs'), public_path('docs/poppy'));
				$this->info('Publish Success!');
				break;
			case 'poppy--r':
				$this->getFile()->copyDirectory(public_path('docs/poppy'), base_path('extensions/ext-fe/resources/docs'));
				$this->info('Publish Success!');
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
		$lower   = strtolower($key);
		$shell   = "apidoc -i " . $path . '  -o ' . $aim;
		$process = new Process($shell);
		$process->start();
		$process->wait(function($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . "\n");
			}
		});
	}
}
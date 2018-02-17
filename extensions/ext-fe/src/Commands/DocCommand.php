<?php namespace Poppy\Extension\Fe\Commands;

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
					$catalog = config('ext-fe.apidoc');
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
			case 'project':
				$aimFolder = public_path('docs/poppy');
				$this->getFile()->copyDirectory(base_path('extensions/ext-fe/resources/docs'), $aimFolder);

				// root readme
				$this->getFile()->copyDirectory(base_path('README.md'), $aimFolder . '/README.md');

				// project common
				$this->getFile()->copyDirectory(base_path('resources/docs'), $aimFolder);

				// extension doc
				$this->modulesDoc();
				$this->extensionDoc();
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
		$filter = data_get($dir, 'filter');
		$f      = '';
		if (is_array($filter) && count($filter)) {
			foreach ($filter as $sf) {
				$f .= ' -f ' . $sf;
			}
		}
		$lower = strtolower($key);
		$shell = "apidoc -i " . $path . '  -o ' . $aim . ' ' . $f;
		$this->info($shell);
		$process = new Process($shell);
		$process->start();
		$process->wait(function($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . "\n");
			}
		});
	}

	private function modulesDoc()
	{
		$aimFolder = public_path('docs/poppy');
		// append modules doc
		$folders = $this->getFile()->directories(base_path('modules/'));
		if (is_array($folders)) {
			foreach ($folders as $folder) {
				$baseFolder = basename($folder);
				$glob       = glob($folder . '/docs/*.md');
				if (!count($glob)) {
					continue;
				}
				foreach ($glob as $file) {
					$baseFilename = basename($file);
					$fileName     = $baseFolder . '-' . $baseFilename;
					$display      = $baseFolder . '/' . FileHelper::removeExtension($baseFilename);

					// make directory
					$folder = $aimFolder . '/' . $baseFolder . '/';
					if (!$this->getFile()->exists($folder)) {
						$this->getFile()->makeDirectory($folder, 0755, true);
					}

					// 复制文件
					$this->getFile()->copy($file, $folder . $fileName);

					// 追加链接
					$sidebarPath = $aimFolder . '/_sidebar.md';
					if (file_exists($sidebarPath)) {
						$linkText = "\n- [{$display}]({$baseFolder}/{$fileName})";
						$this->getFile()->append($sidebarPath, $linkText);
					}
				}
			}
		}
	}

	private function extensionDoc()
	{
		$aimFolder = public_path('docs/poppy');
		// append extension link
		$folders    = $this->getFile()->directories(base_path('extensions/'));
		$readmePath = '';
		if (is_array($folders)) {
			foreach ($folders as $folder) {
				$baseFolder = basename($folder);
				$glob       = glob($folder . '/[Rr][Ee][Aa][Dd][Mm][Ee].md');
				if (count($glob)) {
					$readmePath = current($glob);
				}
				if ($readmePath) {
					$fileName = $baseFolder . '-' . 'readme.md';
					$display  = $baseFolder . '/Readme';

					// make directory
					$folder = $aimFolder . '/extensions/';
					if (!$this->getFile()->exists($folder)) {
						$this->getFile()->makeDirectory($folder, 0755, true);
					}

					// 复制文件
					$this->getFile()->copy($readmePath, $folder . $fileName);

					// 追加链接
					$sidebarPath = $aimFolder . '/_sidebar.md';
					if (file_exists($sidebarPath)) {
						$linkText = "\n- [{$display}](extensions/{$fileName})";
						$this->getFile()->append($sidebarPath, $linkText);
					}
				}
			}
		}
	}
}

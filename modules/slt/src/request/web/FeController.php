<?php namespace Slt\Request\Web;


use Poppy\Framework\Helper\FileHelper;
use Poppy\Framework\Helper\StrHelper;
use Symfony\Component\Process\Process;


class FeController extends InitController
{
	private $selfMenu;

	/**
	 * FeController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->selfMenu = [
			'Js文档'         => [
				'archive' => false,
				'url'     => route('slt:fe.js'),
			],
			'酸柠檬'          => [
				'archive' => false,
				'url'     => '',
			],
			'修复 global 错误' => [
				'archive' => true,
				'url'     => '',
			],
			'js文档'         => [
				'archive' => true,
				'url'     => '',
			],
		];
	}


	/**
	 * 首页
	 * @param null $plugin
	 * @return mixed
	 * @throws \Throwable
	 */
	public function js($plugin = null)
	{
		$disk        = \Storage::disk('public');
		$directories = $disk->directories('assets/js/libs', true);
		$unset       = [];
		$single      = [];
		$jquery      = [];
		$bt3         = [];

		foreach ($directories as $dir) {
			$dir = str_replace('assets/js/libs/', '', $dir);
			if (in_array($dir, $unset)) {
				continue;
			}
			if (strpos($dir, 'jquery') === false) {
				if (strpos($dir, '/') === false) {
					$single[] = $dir;
				}
			}
			elseif (strpos($dir, 'jquery/') !== false) {
				$dir = str_replace('jquery/', '', $dir);
				if (strpos($dir, '/') === false) {
					$jquery[] = $dir;
				}
			}
			elseif (strpos($dir, 'bt3/') !== false) {
				$dir = str_replace('bt3/', '', $dir);
				if (strpos($dir, '/') === false) {
					$bt3[] = $dir;
				}
			}
		}
		sort($single);
		sort($jquery);
		sort($bt3);

		$alphaSplit = function($array, $split = 3) {
			//  计算总数
			sort($array);
			$num     = count($array);
			$partNum = $num / $split;
			$temp    = [];
			for ($i = 1; $i <= $split; $i++) {
				$temp[$i] = [];
				while (is_array($array) && !empty($array)) {
					if (count($temp[$i]) <= $partNum) {
						$temp[$i][] = array_shift($array);
					}
					else {
						break;
					}
				}
				if (!empty($temp[$i])) {
					$clone                                                    = $temp[$i];
					$firstLetter                                              = substr(array_shift($clone), 0, 1);
					$lastLetter                                               = substr(array_pop($clone), 0, 1);
					$temp[ucfirst($firstLetter) . '-' . ucfirst($lastLetter)] = $temp[$i];
					unset($temp[$i]);
				}
			}
			return $temp;
		};


		$singles = $alphaSplit($single, 3);
		$jquerys = $alphaSplit($jquery, 4);
		$bt3s    = $alphaSplit($bt3, 2);
		// 默认插件
		if (!$plugin) {
			$plugin = $single[0];
		}

		if (strpos($plugin, 'bt3') !== false) {
			// bt3
			$viewDir   = 'auto_jquery_bt3';
			$viewName  = substr($plugin, 11);
			$pluginDir = 'assets/js/libs/bt3/' . $viewName;
		}
		elseif (strpos($plugin, 'jquery') !== false) {
			// jquery
			$viewDir   = 'auto_jquery';
			$viewName  = substr($plugin, 7);
			$pluginDir = 'assets/js/libs/jquery/' . $viewName;
		}
		else {
			// common
			$viewDir   = 'auto';
			$viewName  = $plugin;
			$pluginDir = 'assets/js/libs/' . $viewName;
		}

		$viewContent = '';
		$view        = 'slt::fe.js.' . $viewDir . '.' . $viewName;
		if ($this->getView()->exists($view)) {
			$viewContent = view($view)->render();
		}


		$files = $disk->files($pluginDir);

		$markdownPath = '';
		if (is_array($files)) {
			foreach ($files as $file) {
				if (preg_match('/readme.md/i', $file)) {
					$markdownPath = $file;
				}
			}
		}

		$markdownContent = '';
		if ($markdownPath && file_exists($markdownPath)) {
			$content = file_get_contents($markdownPath);
			try {
				$markdownContent = StrHelper::markdownToHtml($content);
			} catch (\Exception $e) {
				die($e->getMessage());
			}

		}


		$data = [
			'view'      => $viewContent,
			'plugin'    => $plugin,
			'singles'   => $singles,
			'jquerys'   => $jquerys,
			'bt3s'      => $bt3s,
			'readme'    => $markdownContent,
			'self_menu' => $this->selfMenu,
		];

		return view('slt::fe.js.auto', $data);
	}


	public function markdown($dir = null)
	{
		$docDir   = resource_path('docs');
		$fullDirs = FileHelper::subDir($docDir);
		$dirs     = [];
		foreach ($fullDirs as $d) {
			$dirs[] = str_replace($docDir . '/', '', $d);
		}

		$dir = urldecode($dir) ?: $dirs[0];

		$file      = \Input::get('file');
		$data      = [];
		$detail    = $this->markdownDetail($dir, $file);
		$content   = FileHelper::get($detail['current']);
		$html      = StrHelper::markdownToHtml($content);
		$html_copy = $html;
		$html_dom  = HtmlDomParser::str_get_html($html_copy);

		$titles   = [];
		$h2_title = '';
		if ($html_dom) {
			foreach ($html_dom->find('h2,h3,table') as $k => $title) {
				if ($title->tag == 'h2') {
					$h2_title                  = $title->plaintext;
					$titles[$title->plaintext] = [];
					$html                      = str_replace('<h2>' . $title->plaintext . '</h2>', '<h2 id="' . md5($title->plaintext) . '">' . $title->plaintext . '</h2>', $html);
				}
				if ($title->tag == 'table') {
					$title->class = 'table';
					$html         = str_replace('<table>', '<table class="table">', $html);
				}
				if ($h2_title && $title->tag == 'h3' && isset($titles[$h2_title])) {
					$titles[$h2_title][] = $title->plaintext;
					$html                = str_replace('<h3>' . $title->plaintext . '</h3>', '<h3 id="' . md5($title->plaintext) . '">' . $title->plaintext . '</h3>', $html);
				}
			}
		}
		$data['html']        = $html;
		$data['detail']      = $detail;
		$data['titles']      = $titles;
		$data['dirs']        = $dirs;
		$data['current_dir'] = $dir;
		$data['self_menu']   = $this->selfMenu;

		return view('web.fe.markdown', $data);
	}

	public function cache()
	{
		@unlink(app_path('assets/js/global.js'));
		header('location:' . route('index'));
	}

	private function markdownDetail($dir, $file)
	{
		$doc_root = resource_path('docs/' . $dir);

		$dirs  = FileHelper::listDir($doc_root);
		$files = [];
		$fb    = '';
		if ($file) {
			$fb = $file;
		}
		foreach ($dirs as $dir) {
			$sub_files = FileHelper::subFile($dir);
			foreach ($sub_files as $k => $v) {
				if (!$fb) {
					$fb = substr($v, strlen($doc_root));
				}
				$sub_files[$k] = substr($v, strlen($doc_root));
			}
			$files[] = [
				'folder' => substr($dir, strlen($doc_root)),
				'files'  => $sub_files,
			];
		}

		$data = [
			'files'   => $files,
			'current' => $doc_root . $fb,
			'fb'      => $fb,
		];

		return $data;
	}

	/**
	 * 首页
	 * @param null $name
	 * @return mixed
	 */
	public function getViews($name = null)
	{
		if (strpos($name, '.') === false) {
			$name = $name . '.index';
		}

		$dir       = str_replace('.', '/', $name);
		$dirs      = explode('/', $dir);
		$project   = $dirs[0];
		$directory = str_replace($project . '/', '', $dir);
		$file      = app_path('project/' . $project . '/views/' . $project . '/' . $directory . '.blade.php');

		if (!file_exists($file)) {
			die('文件' . $file . ' 不存在!');
		}
		return view($name, [
			'project' => $project,
		]);
	}

	public function getImages($name = null)
	{
		$docDir   = app_path('project');
		$fullDirs = FileHelper::subDir($docDir);
		$dirs     = [];
		foreach ($fullDirs as $d) {
			$dirs[] = str_replace($docDir . '/', '', $d);
		}
		if (!$name) {
			$dir = $dirs[0];
		}
		else {
			$dir = $name;
		}
		$imageDir = app_path('project/' . $dir . '/images');
		if (!file_exists($imageDir)) {
			exit ('图片路径' . $imageDir . '不存在!');
		}
		else {
			$images = FileHelper::listDir($imageDir);;
			$files = [];
			foreach ($images as $image) {
				$subFiles = FileHelper::subDir($image);
				$subUrls  = [];
				foreach ($subFiles as $file) {
					$subUrls[] = url(substr($file, strpos($file, 'project/' . $dir)));
				}
				$files[str_replace($imageDir, '', $image)] = $subUrls;
			}
			return view('lemon.project.images', [
				'project' => $dir,
				'images'  => $files,
			]);
		}
	}


	public function run()
	{
		$code_html = \Input::get('html');
		$code_js   = \Input::get('js');
		$code_css  = \Input::get('css');
		$site      = config('app.url');
		$html      = <<<HTML
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>演示文档</title>
    <script src="$site/assets/js/require.js"></script>
    <script src="$site/assets/js/config.js"></script>
    <style>$code_css</style>
</head>
<body>
$code_html
<script>$code_js</script>
</body>
</html>
HTML;
		return \Response::make($html);
	}

	public function postDeploy()
	{
		$shell   = "/bin/bash " . base_path() . 'resources/shell/deploy.sh' . ' ' . base_path() . ' master';
		$process = new Process($shell);
		$process->start();
		$process->wait(function($type, $buffer) {
			if (Process::ERR === $type) {
				echo('ERR > ' . $buffer);
			}
			else {
				echo('OUT > ' . $buffer);
			}
		});
	}
}


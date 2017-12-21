<?php namespace Sour\System\Command;

/**
 * rbac
 * ---- 初始化
 * php artisan lemon:bower init
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2016 Sour Lemon Team
 */

use Illuminate\Console\Command;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\URL;
use Sour\Lemon\Helper\FileHelper;
use Sour\Lemon\Helper\StrHelper;

/**
 * Class Rbac
 * @package Sour\Lemon\Console
 */
class Bower extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'lemon:bower
		{--force= : Force update}
	';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'bower handler.';

	/**
	 * @var string
	 */
	private $bowerComponentsDir;

	private $libPath    = 'assets/js/libs';
	private $scssPath   = 'assets/sass/libs';
	private $configFile = 'assets/js/config.js';
	private $force      = false;
	private $mapData    = [];
	private $dbData     = [];

	/** @var string Js main 文件位置 保留原来位置(默认保留) */
	private $preserveJsPath;

	/** @var  string 当前处理的Js在 bower 中的 name 值 */
	protected $name;

	/** @var  array 当前插件的bower 定义 */
	private $bowerData;

	/** @var \Storage */
	private $disk;

	private $config = [
		'path' => [],
		'shim' => [],
	];

	/**
	 * Execute the console command.
	 */
	public function handle()
	{

		$this->force = (bool) $this->option('force');

		$this->bowerComponentsDir = FileHelper::path(base_path($this->bowerDir()), false);

		$this->disk = \Storage::disk('web');

		$this->mapData = json_decode($this->disk->get('assets/fe_map.json'), true);;

		$directories = FileHelper::listDirectory($this->bowerComponentsDir);
		if (count($directories)) {
			// 多少个任务
			foreach ($directories as $index => $folder) {
				// key    => 我们指定的
				// name   => 官方声明的
				// folder => 文件夹名称, bower 指定的
				$this->dispose($folder, ceil((($index + 1) / count($directories)) * 100));
			}

			// 处理 re libs 的组件
			$this->disposeRefactor();
			$this->writeConfig();
		}
		else {
			$this->error('Directory is empty.');
		}


	}

	/**
	 * 处理文件夹的复制和元素的复制
	 * - js 文件: {js_copy}/{mapped dir}/{version}/{main file}
	 * - css   : {css_copy}/plugin/{mapped dir}.css
	 * - image : {css_copy}/plugin/{mapped dir}/{*.png|*.jpg}
	 * {mapped dir}
	 *      .  =>  -
	 * @param     $plugin_name
	 * @param int $progress
	 */
	private function dispose($plugin_name, $progress = 0)
	{
		$this->bowerData = FileHelper::getJson($this->bowerComponentsDir . '/' . $plugin_name . '/.bower.json', true);
		$this->name      = array_get($this->bowerData, 'name');

		// progress
		$this->line('[' . $this->name . '] Handle ... ');

		// do
		$this->disposeJs();

		// progress
		$mark = str_pad('>', $progress, '>');
		$this->line(str_pad($mark, 100, '='));
	}

	/**
	 * 处理JS 文件
	 */
	private function disposeJs()
	{
		$this->dbData = [];


		$this->preserveJsPath = false;

		// has main config
		$oriMainJsPath = $this->getMainJsPath();
		if (!$oriMainJsPath) {
			$this->error('Does not has main file');
			return;
		}

		// 指定多个 main 文件
		if (is_array($oriMainJsPath)) {

			foreach ($oriMainJsPath as $part_key => $part_path) {
				$oriJsPath = FileHelper::path($this->bowerComponentsDir . '/' . $this->name . '/' . $part_path, false);
				if (!file_exists($oriJsPath) && !$this->force) {
					$this->error('Main File not exist in ' . $oriJsPath);
				}


				// aim path
				$aimFolderName     = $this->aimFolder($this->name);
				$version           = array_get($this->bowerData, 'version') ?: '';
				$aimRelativeFolder = $aimFolderName . '/' . $version;
				$diskMainJsPath    = $this->libPath . '/' . $aimRelativeFolder . '/' . ($this->preserveJsPath ? $oriMainJsPath : basename($oriJsPath));

				// push config
				$aimKeyName                                          = $this->aimName($this->name);
				$aimMainJsPath                                       = str_replace([dirname($this->libPath) . '/', '.js'], '', $diskMainJsPath);
				$this->config['path'][$aimKeyName . '.' . $part_key] = $aimMainJsPath;

				// check main file
				if ($this->disk->exists($diskMainJsPath) && !$this->force) {
					$this->warn('Skip: ' . $diskMainJsPath . '. File exists!');
					continue;
				}
				// copy main js
				$content = FileHelper::get($oriJsPath);
				$this->disk->put($diskMainJsPath, $content);
				$this->info('Write ' . $diskMainJsPath . ' Success.');
			}
			return;
		}

		$oriJsPath = FileHelper::path($this->bowerComponentsDir . '/' . $this->name . '/' . $oriMainJsPath, false);
		if (!file_exists($oriJsPath) && !$this->force) {
			$this->error('Main File not exist in ' . $oriJsPath);
			return;
		}


		// aim path
		$aimFolderName     = $this->aimFolder($this->name);
		$version           = array_get($this->bowerData, 'version') ?: '';
		$aimRelativeFolder = $aimFolderName . ($version ? '/' . $version : '');
		$diskMainJsPath    = $this->libPath . '/'
			. ($aimRelativeFolder ? $aimRelativeFolder . '/' : '')
			. ($this->preserveJsPath ? $oriMainJsPath : basename($oriJsPath));

		// push config
		$aimKeyName                        = $this->aimName($this->name);
		$aimMainJsPath                     = str_replace([dirname($this->libPath) . '/'], '', $diskMainJsPath);
		$this->config['path'][$aimKeyName] = FileHelper::removeExtension($aimMainJsPath);

		// get shim/dep info
		$shim = '';
		if (isset($this->mapData['bower'][$this->name])) {
			$shim = array_get($this->mapData['bower'][$this->name], 'shim');
			if ($shim) {
				$this->config['shim'][$aimKeyName] = $shim;
			}
		}

		$this->cacheDb(1, $aimKeyName, $aimMainJsPath, $shim);

		// get append info
		if (isset($this->mapData['bower'][$this->name])) {
			$append = array_get($this->mapData['bower'][$this->name], 'append');
			if ($append) {
				foreach ($append as $key => $value) {
					// path
					$path                             = str_replace([dirname($this->libPath) . '/'], '', $this->libPath . '/' . $aimRelativeFolder . '/' . $value);
					$appendKey                        = $aimKeyName . '.' . $key;
					$appendPath                       = FileHelper::path($path, false);
					$this->config['path'][$appendKey] = $appendPath;
					$this->cacheDb(0, $appendKey, $appendPath);
				}
			}
		}


		// check main file
		if ($this->disk->exists($diskMainJsPath) && !$this->force) {
			$this->warn('Skip: ' . $diskMainJsPath . '. File exists!');
			return;
		}

		// copy addon
		$this->disposeAddon($this->bowerComponentsDir . '/' . $this->name, $aimRelativeFolder);

		// copy readme
		$oriJsFolder = dirname($oriJsPath);
		$content     = '';
		if (file_exists($oriJsFolder . '/README.md')) {
			$content = FileHelper::get($oriJsFolder . '/README.md');
		}
		elseif (file_exists($oriJsFolder . '/readme.md')) {
			$content = FileHelper::get($oriJsFolder . '/readme.md');
		}
		if ($content) {
			$this->disk->put($this->libPath . '/' . $aimFolderName . '/README.md', $content);
		}

		// copy main js
		$content = FileHelper::get($oriJsPath);
		$this->disk->put($diskMainJsPath, $content);
		$this->info('Write ' . $diskMainJsPath . ' Success.');
	}


	/**
	 * 对扩展进行处理
	 * addon/|js;lib/|css:1
	 * {key}|{extension}:{remove level}
	 * @param $ori_folder
	 * @param $version_folder
	 */
	private function disposeAddon($ori_folder, $version_folder)
	{
		// get plugin info
		if (!isset($this->mapData['bower'][$this->name])) {
			return;
		}
		$addon = array_get($this->mapData['bower'][$this->name], 'addon');
		if (!$addon) {
			return;
		}
		$arrAddon = StrHelper::parseKey($addon);

		$files = FileHelper::listFile($ori_folder);

		// get all files
		foreach ($files as $file) {
			$relative = str_replace($ori_folder . '/', '', $file);

			// filter folder
			foreach ($arrAddon as $folder => $extension) {
				if (
					(strpos($relative, '/') !== false && substr($relative, 0, strlen($folder)) == $folder) ||
					(strpos($relative, '/') === false)
				) {

					if (strpos($extension, ':') !== false) {
						$level     = substr($extension, -1, 1);
						$extension = rtrim($extension, ':' . $level);
					}
					else {
						$level = 0;
					}
					$extensions = explode(',', $extension);
					if (is_file($file)) {

						// filter extension
						if (!$extension || in_array(pathinfo($file, PATHINFO_EXTENSION), $extensions)) {


							for ($l = 0; $l < $level; $l++) {
								$trim     = substr($relative, 0, strpos($relative, '/') + 1);
								$trimLen = strlen($trim);
								$relative = substr($relative, $trimLen);
							}

							// copy to project folder
							$cssPath = $this->libPath . '/' . $version_folder . '/' . $relative;
							if (!$this->disk->exists($cssPath) || $this->force) {
								$this->cacheDb(false, '', $cssPath);
								$content = FileHelper::get($file);
								$this->disk->put($cssPath, $content);
								$this->info('Put ' . $cssPath . ' Success ');
							}

							// write copy file for scss use
							if (pathinfo($file, PATHINFO_EXTENSION) == 'css') {
								$scssPath = $this->scssPath . '/' . $version_folder . '/' . $relative;
								$basename = basename($scssPath);

								// scss file has '_' prefix
								$scssName = '_' . str_replace('.css', '.scss', $basename);
								$scssPath = str_replace($basename, $scssName, $scssPath);
								if (!$this->disk->exists($scssPath) || $this->force) {
									$this->cacheDb(false, '', $cssPath);
									$content = $this->disposeCss($file, dirname($cssPath));
									$this->disk->put($scssPath, $content);
									$this->info('Put ' . $scssPath . ' Success ');
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * css
	 * @param $css_file
	 * @param $version_folder
	 * @return string
	 */
	private function disposeCss($css_file, $version_folder)
	{
		$content = file_get_contents($css_file);
		$content = preg_replace_callback('/url\(([\'"]?)(.*?)\1\)/i', function ($matches) use ($version_folder) {
			$match = $matches[2];
			if ($match == 'about:blank' || strpos($match, 'data:image/png') !== false) {
				return 'url("' . $match . '")';
			}
			else {
				return 'url("/' . $version_folder . '/' . $match . '")';
			}
		}, $content);

		$opc     = new Parser($content);
		$oCss    = $opc->parse();
		$oFormat = OutputFormat::create()->indentWithSpaces(4)->setSpaceBetweenRules("\n");
		return $oCss->render($oFormat);
	}

	/**
	 * 图片背景替换 / 轮询
	 * @param $obj
	 * @param $version_folder
	 */
	private function imageReWrite(&$obj, $version_folder)
	{
		if ($obj instanceof URL) {
			$url = $obj->getURL()->getString();
			if ($url == 'about:blank') {
				return;
			}
			elseif (strpos($url, 'data:image/png') !== false) {
				return;
			}
			else {
				$obj->setURL(new CSSString('/' . $version_folder . '/' . $url));
			}
		}
		if ($obj instanceof RuleValueList) {
			foreach ($obj->getListComponents() as $subOjb) {
				$this->imageReWrite($subOjb, $version_folder);
			}
		}
	}


	/**
	 * 目标文件名
	 * @param $folder
	 * @return mixed
	 */
	private function aimFolder($folder)
	{
		// 检查映射, 为了避免 datatables 中存在 . 号, 使用原生php获取
		$bower = array_get($this->mapData, 'bower');
		$map   = isset($bower[$folder]) ? $bower[$folder] : [];
		if ($map && isset($map['folder']) && $map['folder']) {
			$folder = $map['folder'];
		}

		// 检查文件夹名规范
		// . => -
		// snake style
		$folder = snake_case($folder);
		return str_replace(['.', '_'], ['-'], $folder);
	}

	private function aimName($folder)
	{
		$folder = $this->aimFolder($folder);
		return str_replace('/', '.', $folder);
	}

	/**
	 * 获取 bower 的路径
	 * @return string
	 */
	private function bowerDir()
	{
		// bowerRc
		$bowerRc = base_path('.bowerrc');
		if (!file_exists($bowerRc)) {
			// 默认的目录
			return 'bower_components/';
		}
		$arrBowerRc = FileHelper::getJson($bowerRc);
		$bowerDir   = base_path(array_get($arrBowerRc, 'directory'));
		if (!FileHelper::isDir($bowerDir)) {
			$this->error('Path ' . $bowerDir . ' not exist.');
		}
		$this->info('Bower Path : ' . $bowerDir);
		return array_get($arrBowerRc, 'directory');
	}

	/**
	 * 写入配置文件
	 */
	private function writeConfig()
	{
		ksort($this->config['path']);
		ksort($this->config['shim']);
		$alias  = json_encode($this->config['path'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$shim   = json_encode($this->config['shim'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$config = <<<CONFIG
var alias = $alias;

var shim  = $shim;

// appends for single project
// noinspection JSUnresolvedVariable
if (typeof appends != 'undefined' && typeof appends == 'object') {
	for (var k in appends) {
		alias[k] = appends[k];
	}
}

// require js config
requirejs.config({
	baseUrl: '/assets/js',
	paths  : alias,
	shim   : shim
});
CONFIG;
		$this->disk->put($this->configFile, $config);
	}

	private function getMainJsPath()
	{
		$main          = array_get($this->bowerData, 'main');
		$oriMainJsPath = array_get($this->mapData, 'bower.' . $this->name . '.main');
		if ($oriMainJsPath) {
			if (is_string($oriMainJsPath) && strpos($oriMainJsPath, '|') !== false) {
				$oriMainJsPath        = substr($oriMainJsPath, 0, strpos($oriMainJsPath, '|'));
				$this->preserveJsPath = true;
			}

			if (is_array($oriMainJsPath)) {
				return $oriMainJsPath;
			}
		}
		if (!$oriMainJsPath) {
			// ori path
			if (is_string($main)) {
				// 检查 key map
				$oriMainJsPath = $main;
			}
			elseif (is_array($main)) {
				$jsCount = 0;
				$jsFiles = [];
				foreach ($main as $js) {
					if (strpos($js, '.js') !== false) {
						$jsCount   += 1;
						$jsFiles[] = $js;
					}
				}
				// 数量多, 需要指定一个 main file
				if (count($jsFiles) > 1) {
					$this->warn('Has Many Main File , You Must define one by your self!');
					$oriMainJsPath = array_get($this->mapData, 'bower.' . $this->name . '.main');
				}
				else {
					$oriMainJsPath = $jsFiles[0];
				}
			}
		}
		return $oriMainJsPath;
	}

	/**
	 * @param bool   $is_main
	 * @param string $key
	 * @param string $path
	 * @param string $shim
	 */
	private function cacheDb($is_main, $key, $path, $shim = '')
	{
		$extension      = pathinfo($path, PATHINFO_EXTENSION);
		$this->dbData[] = [
			'group'       => $this->name,
			'is_main'     => (int) $is_main,
			'shim'        => $shim ? serialize($shim) : '',
			'extension'   => $extension,
			'key'         => $key,
			'path'        => $path,
			'description' => '',
			'github_url'  => '',
			'cat_id'      => 0,
			'tag_ids'     => '',
			'readme_ori'  => '',
			'readme_zh'   => '',
		];
	}

	private function disposeRefactor()
	{
		$reLibs = array_get($this->mapData, 're_libs');
		if (is_array($reLibs)) {
			foreach ($reLibs as $lib_key => $lib) {
				if ($main = array_get($lib, 'main')) {
					$path                           = 're_libs/' . $main;
					$this->config['path'][$lib_key] = FileHelper::removeExtension($path);
				}
				if ($shim = array_get($lib, 'shim')) {
					$this->config['shim'][$lib_key] = $shim;
				}
			}
		}
	}

}

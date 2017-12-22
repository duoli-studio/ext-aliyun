<?php namespace Poppy\Extension\Fe\Console;


use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\FileHelper;
use Poppy\Framework\Helper\StrHelper;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\URL;

class Bower extends Command
{
	use PoppyTrait;

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'ext:fe:bower
		{--force= : Force update}
	';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'bower handler.';


	private $libPath;
	private $jsPath;
	private $scssPath;
	private $cssPath;
	private $fontPath;
	private $configFile;
	private $requireFile;
	private $globalFile;
	private $force   = false;
	private $mapData = [];

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
	 * @throws FileNotFoundException
	 */
	public function handle()
	{
		$this->jsPath      = config('ext-fe.folder.js_dir', 'assets/js');
		$this->libPath     = $this->jsPath . '/libs';
		$this->scssPath    = config('ext-fe.folder.scss_dir', 'assets/sass') . '/libs';
		$this->cssPath     = config('ext-fe.folder.css_dir', 'assets/css') . '/libs';
		$this->fontPath    = config('ext-fe.folder.font_dir', 'assets/font');
		$this->configFile  = $this->jsPath . '/config.js';
		$this->requireFile = $this->jsPath . '/require.js';
		$this->globalFile  = $this->jsPath . '/global.js';

		$this->force = (bool) $this->option('force');


		$this->mapData = config('ext-fe');

		$disk = config('ext-fe.disk');
		try {
			if (!$disk) {
				$this->error($this->getKey('fe') . 'No disk defined at `ext-fe` config file');
				return;
			}
			$this->disk = \Storage::disk($disk);
		} catch (\Exception $e) {
			$this->error($this->getKey('fe') . 'No disk `' . $disk . '` defined at `filesystems` config file');
			return;
		}


		$directories = $this->getFile()->directories($this->bowerDir());
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

		$this->disposeRequire();
		$this->writeGlobal();

	}

	/**
	 * 处理文件夹的复制和元素的复制
	 * - js 文件: {js_copy}/{mapped dir}/{version}/{main file}
	 * - css   : {css_copy}/plugin/{mapped dir}.css
	 * - image : {css_copy}/plugin/{mapped dir}/{*.png|*.jpg}
	 * {mapped dir}
	 *      .  =>  -
	 * @param     $folder
	 * @param int $progress
	 * @throws FileNotFoundException
	 */
	private function dispose($folder, $progress = 0)
	{
		$this->bowerData = FileHelper::getJson($folder . '/.bower.json', true);
		$this->name      = array_get($this->bowerData, 'name');

		// progress
		$this->line($this->getKey($this->name) . ' Handle ... ');

		// do
		$this->disposeJs($folder);
		$this->disposeCss($folder);
		$this->disposeFont($folder);

		// progress
		$mark = str_pad('>', $progress, '>');
		$this->line(str_pad($mark, 100, '='));
	}


	private function getBowerName()
	{
		return array_get($this->bowerData, 'name');
	}

	/**
	 * @throws FileNotFoundException
	 */
	private function disposeRequire()
	{
		$key = $this->getKey('requirejs');
		$this->line($key . ' Handle ... ');

		if (!$this->disk->exists($this->requireFile)) {
			$this->disk->put($this->requireFile, $this->getFile()->get(__DIR__ . '/../../resources/mixes/require.js'));
		}

		$this->info($key . ' Write Ok');
	}

	/**
	 * 处理JS 文件
	 * @param $folder
	 * @throws FileNotFoundException
	 */
	private function disposeJs($folder)
	{
		$key = $this->getKey($this->name);

		// has main config
		$oriMainJsPath = $this->getMainJsPath();
		$aimJsPath     = $this->getAimPath();
		if (!$oriMainJsPath || !$aimJsPath) {
			$this->error($key . ' Does not has main file or aim path');
			return;
		}


		$realJsPath = $folder . '/' . $oriMainJsPath;
		if (!$this->getFile()->exists($realJsPath) && !$this->force) {
			$this->error($key . ' Main File not exist in ' . $realJsPath);
		}

		// check main file
		if ($this->disk->exists($aimJsPath) && !$this->force) {
			$this->warn($key . ' Skip: ' . $aimJsPath . '. File exists!');
		}
		else {
			$this->disk->put($aimJsPath, $this->getFile()->get($realJsPath));
			$this->info($key . ' Write ' . $aimJsPath . ' Success.');
		}

		$configKey                        = $this->getConfigKey();
		$this->config['path'][$configKey] = FileHelper::removeExtension($aimJsPath);

		// get shim/dep info
		if (isset($this->mapData['bower'][$this->name])) {
			$shim = array_get($this->mapData['bower'][$this->name], 'shim');
			if ($shim) {
				$this->config['shim'][$configKey] = $shim;
			}
		}
	}


	/**
	 * css
	 * @param $folder
	 * @return string|void
	 * @throws FileNotFoundException
	 */
	private function disposeCss($folder)
	{
		$cssMap = data_get($this->mapData, 'bower.' . $this->name . '.css');
		if (!$cssMap) {
			return;
		}
		foreach ($cssMap as $key => $item) {
			$isFile = (bool) $this->getFile()->extension($item);
			$aim    = $this->getVersion($this->cssPath . '/' . $item);
			if ($isFile) {
				$content = $this->getFile()->get($folder . '/' . $key);
				if ($this->disk->exists($aim)) {
					$this->warn($this->getKey($this->name) . 'Skip: File `' . $aim . '` exists!');
					continue;
				}
				$this->disk->put($aim, $content);
			}
			else {
				$files = $this->getFile()->glob($folder . '/' . $key);
				foreach ($files as $res_file) {
					$aimFile = $aim . '/' . basename($res_file);
					if ($this->disk->exists($aimFile)) {
						$this->warn($this->getKey($this->name) . 'Skip: File `' . $aimFile . '` exists!');
						continue;
					}
					$content = $this->getFile()->get($res_file);
					$this->disk->put($aimFile, $content);
				}
			}
		}

		$mapUrlContent = function ($content, $version) {
			$content = preg_replace_callback('/url\(([\'"]?)(.*?)\1\)/i', function ($matches) use ($version) {
				$match = $matches[2];
				if ($match == 'about:blank' || strpos($match, 'data:image/png') !== false) {
					return 'url("' . $match . '")';
				}
				else {
					return 'url("/' . $version . '/' . $match . '")';
				}
			}, $content);

			$opc     = new Parser($content);
			$oCss    = $opc->parse();
			$oFormat = OutputFormat::create()->indentWithSpaces(4)->setSpaceBetweenRules("\n");
			return $oCss->render($oFormat);
		};
	}

	/**
	 * @param $folder
	 * @throws FileNotFoundException
	 */
	private function disposeFont($folder)
	{
		$cssMap = data_get($this->mapData, 'bower.' . $this->name . '.font');
		if (!$cssMap) {
			return;
		}
		foreach ($cssMap as $key => $item) {
			$isFile = (bool) $this->getFile()->extension($item);
			$aim    = $this->getVersion($this->fontPath . '/' . $item);
			if ($isFile) {
				$content = $this->getFile()->get($folder . '/' . $key);
				if ($this->disk->exists($aim)) {
					$this->warn($this->getKey($this->name) . 'Skip: File `' . $aim . '` exists!');
					continue;
				}
				$this->disk->put($aim, $content);
			}
			else {
				$files = $this->getFile()->glob($folder . '/' . $key);
				foreach ($files as $res_file) {
					$aimFile = $aim . '/' . basename($res_file);
					if ($this->disk->exists($aimFile)) {
						$this->warn($this->getKey($this->name) . 'Skip: File `' . $aimFile . '` exists!');
						continue;
					}
					$content = $this->getFile()->get($res_file);
					$this->disk->put($aimFile, $content);
				}
			}
		}
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

	/**
	 * 获取 bower 的路径
	 * @return string
	 * @throws FileNotFoundException
	 */
	private function bowerDir()
	{
		// bowerRc
		$bowerRc = base_path('.bowerrc');
		if (!file_exists($bowerRc)) {
			// 默认的目录
			return base_path('bower_components');
		}
		$arrBowerRc = json_decode($this->getFile()->get($bowerRc));
		$bowerDir   = base_path(data_get($arrBowerRc, 'directory'));
		if (!$this->getFile()->isDirectory($bowerDir)) {
			$this->error('Path ' . $bowerDir . ' not exist.');
		}
		$this->info('Bower Path : ' . $bowerDir);
		return base_path(data_get($arrBowerRc, 'directory'));
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
	baseUrl: "/",
	paths  : alias,
	shim   : shim
});
CONFIG;
		$this->disk->put($this->configFile, $config);
	}

	private function writeGlobal()
	{
		$global = json_encode(data_get($this->mapData, 'global'), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$js     = <<<JS
define(function(){
	return $global;
});
JS;
		$this->disk->put($this->globalFile, $js);
	}

	private function getMainJsPath()
	{
		$main   = data_get($this->bowerData, 'main');
		$jsData = data_get($this->mapData, 'bower.' . $this->name . '.js');
		if (isset($jsData['main'])) {
			return $jsData['main'];
		}

		// ori path
		if (is_string($main)) {
			// 检查 key map
			return $main;
		}
		return '';
	}

	private function getAimPath()
	{
		$jsData = data_get($this->mapData, 'bower.' . $this->name . '.js');
		if (isset($jsData['aim'])) {
			return $this->getVersion($this->libPath . '/' . $jsData['aim']);
		}
		return '';
	}


	private function getVersion($item)
	{
		$version = array_get($this->bowerData, 'version') ?: '';
		return str_replace('{VERSION}', $version, $item);
	}

	private function getConfigKey()
	{
		$key = data_get($this->mapData, 'bower.' . $this->name . '.key');
		if ($key) {
			return $key;
		}
		return $this->name;
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

	private function getKey($key = '')
	{
		return '[Extension-fe' . ($key ? ':' . $key : '') . '] ';
	}
}

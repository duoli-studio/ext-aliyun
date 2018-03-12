<?php namespace System\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\FileHelper;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\URL;

class BowerCommand extends Command
{
	use PoppyTrait;

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'system:bower
		{--force= : Force update}
		{--debug= : debug}
	';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'bower handler.';

	private $jsLibPath;
	private $jsPath;
	private $imagePath;
	private $scssPath;
	private $cssPath;
	private $fontPath;
	private $jsConfigFile;
	private $jsGlobalFile;
	private $force   = false;
	private $debug   = false;
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
		$this->jsPath       = config('fe.folder.js_dir', 'assets/js');
		$this->jsLibPath    = $this->jsPath . '/libs';
		$this->imagePath    = config('fe.folder.image_dir', 'assets/css_images');
		$this->scssPath     = config('fe.folder.scss_dir', 'assets/sass') . '/libs';
		$this->cssPath      = config('fe.folder.css_dir', 'assets/css') . '/libs';
		$this->fontPath     = config('fe.folder.font_dir', 'assets/font');
		$this->jsConfigFile = $this->jsPath . '/config.js';
		$this->jsGlobalFile = $this->jsPath . '/global.js';

		$this->force = (bool) $this->option('force');
		$this->debug = (bool) $this->option('debug');


		$this->config['path']['global'] = $this->jsPath . '/global';

		$this->mapData = config('fe');

		$disk = config('fe.disk');
		try {
			if (!$disk) {
				$this->error($this->getKey('fe') . 'No disk defined at `fe` config file');
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
		$this->line($this->getKey($this->name) . 'Handle ...');
		// do
		$this->disposeJs($folder);
		$this->disposeCss($folder);
		$this->disposeFont($folder);

		// progress
		$mark = str_pad('>', $progress, '>');
		$this->line(str_pad($mark, 100, '='));
	}


	/**
	 * 处理JS 文件
	 * @param $folder
	 * @throws FileNotFoundException
	 */
	private function disposeJs($folder)
	{

		$map = isset($this->mapData['bower'][$this->name]['js'])
			? $this->mapData['bower'][$this->name]['js'] : '';

		if (!$map) {
			return;
		}

		$key = $this->getKey($this->name);

		$main = data_get($this->bowerData, 'main');

		// get main js
		$oriMainJsPath = '';
		if (isset($map['main'])) {
			$oriMainJsPath = $map['main'];
		}
		// ori path
		if (is_string($main)) {
			// 检查 key map
			$oriMainJsPath = $main;
		}

		// aim js
		$aimJsPath = '';
		if (isset($map['aim'])) {
			$aimJsPath = $this->getVersion($this->jsLibPath . '/' . $map['aim']);
		}
		if (!$oriMainJsPath || !$aimJsPath) {
			$this->error($key . 'Does not has main file or aim path');
			return;
		}

		$realJsPath = $folder . '/' . $oriMainJsPath;
		if (!$this->getFile()->exists($realJsPath) && !$this->force) {
			$this->error($key . 'Main File not exist in ' . $realJsPath);
		}

		// check main file
		if ($this->disk->exists($aimJsPath) && !$this->force) {
			$this->warn($key . 'Skip Js ' . $aimJsPath . '. file exists!');
		}
		else {
			$this->disk->put($aimJsPath, $this->getFile()->get($realJsPath));
			$this->info($key . 'Write Js' . $aimJsPath . ' Success.');
		}

		$this->disposeReadme($folder, dirname($aimJsPath));

		$configKey                        = $this->getConfigKey();
		$this->config['path'][$configKey] = FileHelper::removeExtension($aimJsPath);

		$map = isset($this->mapData['bower'][$this->name]['js']['config']) ?
			$this->mapData['bower'][$this->name]['js']['config'] : [];
		if ($map && is_array($map) && count($map)) {
			foreach ($map as $confKey => $item) {
				if ($confKey == '__same') {
					$confKey = $configKey;
				}
				$this->config['path'][$confKey] = dirname(FileHelper::removeExtension($aimJsPath)) . $item;
			}
		}

		// get shim/dep info
		if (isset($this->mapData['bower'][$this->name])) {
			$shim = array_get($this->mapData['bower'][$this->name], 'shim');
			if ($shim) {
				$this->config['shim'][$configKey] = $shim;
			}
		}

		$map = data_get($this->mapData, 'bower.' . $this->name . '.js.dispose');
		if ($map && is_array($map) && count($map)) {
			foreach ($map as $ori_key => $item) {
				$aim_key = $this->getVersion($item);
				$this->disposeItem($this->jsLibPath, $aim_key, $folder, $ori_key);
			}
		}

		$this->info($key . 'Handle Js Success');
	}


	/**
	 * css
	 * @param $ori_folder
	 * @return string|void
	 * @throws FileNotFoundException
	 */
	private function disposeCss($ori_folder)
	{
		$cssMap = $this->mapData['bower'][$this->name]['css'] ?? [];
		if (!$cssMap) {
			return;
		}
		foreach ($cssMap as $ori_path => $aim_path) {
			$relative_path = $this->getVersion($aim_path);
			$this->disposeItem($this->scssPath, $relative_path, $ori_folder, $ori_path);
		}
		$this->info($this->getKey($this->name) . 'Handle Css Success');
	}

	/**
	 * css
	 * @param string $ori_folder
	 * @param string $aim_folder
	 * @return string|void
	 * @throws FileNotFoundException
	 */
	private function disposeReadme($ori_folder, $aim_folder)
	{
		$readme = $this->getFile()->glob($ori_folder . '/[rR][eE][aA][dD][mM][eE].[mM][dD]');
		if (isset($readme[0])) {
			$readmeFile = $readme[0];
			$readmeAim  = $aim_folder . '/README.md';
			if (!$this->disk->exists($readmeAim)) {
				$content = $this->getFile()->get($readmeFile);
				$this->disk->put($readmeAim, $content);
				$this->info($this->getKey($this->name) . 'Write Readme.md Success');
			}
		}
	}


	/**
	 * @param $aim_folder
	 * @param $aim_key
	 * @param $ori_path
	 * @param $ori_key
	 * @throws FileNotFoundException
	 */
	private function disposeFolder($aim_folder, $aim_key, $ori_path, $ori_key)
	{
		$ori_scan = str_replace('//', '/', $ori_path . '/' . $ori_key);
		$ori_path = str_replace('//', '/', $ori_path);

		$files = $this->getFile()->glob($ori_scan);


		foreach ($files as $res_file) {
			// /dist/css/bootstrap-theme.css
			$relative_file = str_replace($ori_path, '', $res_file);
			$ori_dirname   = dirname($ori_key);
			if ($ori_dirname != '.') {
				$search  = [$ori_dirname, '//'];
				$replace = [$aim_key, '/'];
			}
			else {
				$search  = ['//'];
				$replace = ['/'];
			}
			$aim_file = $aim_folder . '/' . trim(str_replace($search, $replace, $relative_file), '/');

			if ($this->getFile()->isFile($res_file)) {
				$this->disposeFile($aim_file, $res_file);
			}
			else {

				$relative_path  = str_replace([$ori_path, $ori_dirname, '//'], ['', '', '/'], $res_file);
				$relative_match = basename($ori_key);

				$re_aim_key = str_replace('//', '/', $aim_key . $relative_path . '/');
				$re_ori_key = str_replace('//', '/', $ori_dirname . $relative_path . '/' . $relative_match);

				$this->line($this->getKey($this->name) . 'Folder: ' . $re_aim_key);
				$this->disposeFolder($aim_folder, $re_aim_key, $ori_path, $re_ori_key);
			}
		}
	}

	/**
	 * @param $aim_file
	 * @param $res_file
	 * @throws FileNotFoundException
	 */
	private function disposeFile($aim_file, $res_file)
	{
		$key = 'Skip: ';
		if (!$this->disk->exists($aim_file)) {

			$extension = $this->getFile()->extension($aim_file);
			$content   = $this->getFile()->get($res_file);
			if ($extension == 'css') {
				// aim file : assets/css/libs/jquery/layer/moon/style.css
				// match    : default.png
				$mapUrlContent = function($content, $aim_file) {
					$content = preg_replace_callback('/url\(([\'"]?)(.*?)\1\)/i', function($matches) use ($aim_file) {
						$match = $matches[2];


						if ($match == 'about:blank' || strpos($match, 'data:image/png') !== false) {
							return 'url("' . $match . '")';
						}
						else {
							$relative_path = dirname($aim_file) . '/' . $match;
							return 'url("/' . $relative_path . '")';
						}
					}, $content);


					$opc     = new Parser($content);
					$oCss    = $opc->parse();
					$oFormat = OutputFormat::create()->indentWithSpaces(4)->setSpaceBetweenRules("\n");
					return $oCss->render($oFormat);
				};

				$content = $mapUrlContent($content, $aim_file);
			}

			$this->disk->put($aim_file, $content);
			$key = 'Write: ';
		}
		if ($this->debug) {
			$method = $key == 'Skip: ' ? 'warn' : 'info';
			$this->$method($this->getKey($this->name) . $key . 'File `' . $aim_file . '`');
		}
	}

	/**
	 * @param $aim_folder
	 * @param $aim_key
	 * @param $ori_path
	 * @param $ori_key
	 * @throws FileNotFoundException
	 */
	private function disposeItem($aim_folder, $aim_key, $ori_path, $ori_key)
	{
		if (substr($aim_key, -1) === '/') {
			$this->disposeFolder($aim_folder, $aim_key, $ori_path, $ori_key);
		}
		else {
			$this->disposeFile($aim_folder . '/' . $aim_key, $ori_path . '/' . $ori_key);
		}
	}

	/**
	 * @param $folder
	 * @throws FileNotFoundException
	 */
	private function disposeFont($folder)
	{
		$map = data_get($this->mapData, 'bower.' . $this->name . '.font');
		if (!$map) {
			return;
		}
		$this->line($this->getKey($this->name) . 'Handle Font ...');
		foreach ($map as $oriKey => $item) {
			$aimKey = $this->getVersion($item);
			$this->disposeItem($this->fontPath, $aimKey, $folder, $oriKey);
		}
		$this->info($this->getKey($this->name) . 'Handle Font Success');
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
		$this->line($this->getKey('config') . 'Handle ... ');
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
		$this->disk->put($this->jsConfigFile, $config);
		$this->disk->put($this->jsPath . '/config.json', json_encode($this->config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		$this->info($this->getKey('config') . 'Write OK');
	}

	private function writeGlobal()
	{
		$global = json_encode(data_get($this->mapData, 'global'), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$js     = <<<JS
define(function(){
	return $global;
});
JS;
		$this->disk->put($this->jsGlobalFile, $js);
	}


	private function getVersion($item)
	{
		$version = array_get($this->bowerData, 'version') ?: '';
		return str_replace('{VERSION}', $version, $item);
	}

	private function getConfigKey()
	{
		return $this->mapData['bower'][$this->name]['key'] ?? $this->name;
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

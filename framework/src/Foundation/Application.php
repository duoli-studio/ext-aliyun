<?php namespace Poppy\Framework\Foundation;

use Closure;
use Exception;
use Illuminate\Foundation\Application as ApplicationBase;
use Poppy\Framework\Addon\AddonServiceProvider;
use Poppy\Framework\Console\ConsoleServiceProvider;
use Poppy\Framework\Console\GeneratorServiceProvider;
use Poppy\Framework\Extension\ExtensionServiceProvider;
use Poppy\Framework\GraphQL\GraphQLServiceProvider;
use Poppy\Framework\Module\ModuleServiceProvider;
use Poppy\Framework\Parse\ParseServiceProvider;
use Poppy\Framework\Poppy\PoppyServiceProvider;
use Poppy\Framework\Providers\BladeServiceProvider;
use Poppy\Framework\Support\HelperServiceProvider;
use Poppy\Framework\Translation\TranslationServiceProvider;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Throwable;

class Application extends ApplicationBase
{

	const VERSION = '0.1.0';

	/**
	 * 请求执行上下文
	 * @var string
	 */
	protected $executionContext;

	protected $addonPath;

	protected $modulesPath;

	public function registerBaseServiceProviders()
	{
		parent::registerBaseServiceProviders();

		$this->register(ConsoleServiceProvider::class);
		$this->register(GeneratorServiceProvider::class);
		$this->register(BladeServiceProvider::class);
		$this->register(HelperServiceProvider::class);
		$this->register(PoppyServiceProvider::class);
		$this->register(ParseServiceProvider::class);
		$this->register(TranslationServiceProvider::class);
		$this->register(GraphQLServiceProvider::class);
		$this->register(ModuleServiceProvider::class);
		$this->register(AddonServiceProvider::class);
		$this->register(ExtensionServiceProvider::class);
	}


	/**
	 * 绑定路径到 container
	 * @return void
	 */
	protected function bindPathsInContainer()
	{
		parent::bindPathsInContainer();

		$this->instance('path.framework', $this->frameworkPath());
		$this->instance('path.module', $this->modulePath());
		$this->instance('path.extension', $this->extensionPath());
		$this->instance('path.addon', $this->addonPath());
	}


	/**
	 * register "matched" event
	 * @param $callback
	 * @return void
	 */
	public function routeMatched($callback)
	{
		$this['router']->matched($callback);
	}

	/**
	 * 注册错误处理器
	 * @param  \Closure $callback
	 */
	public function error(Closure $callback)
	{
		$this->make('Illuminate\Contracts\Debug\ExceptionHandler')->error($callback);
	}

	/**
	 * 注册严重错误处理器
	 * @param  \Closure $callback
	 */
	public function fatal(Closure $callback)
	{
		$this->error(function (FatalErrorException $e) use ($callback) {
			return call_user_func($callback, $e);
		});
	}

	/**
	 * 检测运行上下文
	 * @return bool
	 */
	public function runningInBackend()
	{
		return $this->executionContext == 'backend';
	}

	/**
	 * 设置运行上下文
	 * @param  string $context
	 * @return void
	 */
	public function setExecutionContext($context)
	{
		$this->executionContext = $context;
	}

	/**
	 * 检测数据库是否链接
	 * @return boolean
	 */
	public function hasDatabase()
	{
		try {
			$this['db.connection']->getPdo();
		} catch (Throwable $ex) {
			return false;
		}
		return true;
	}


	/**
	 * Get application installation status.
	 * @return bool
	 */
	public function isInstalled()
	{
		if ($this->bound('installed')) {
			return true;
		}
		else {
			if (!file_exists($this->storagePath() . DIRECTORY_SEPARATOR . 'installed')) {
				return false;
			}
			$this->instance('installed', true);
			return true;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Laravel framework Config Path
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get cached config path.
	 * @return string
	 */
	public function getCachedConfigPath()
	{
		return $this['path.storage'] . '/framework/config.php';
	}

	/**
	 * Get cached routes path.
	 * @return string
	 */
	public function getCachedRoutesPath()
	{
		return $this['path.storage'] . '/framework/routes.php';
	}

	/**
	 * Get cached packages path.
	 * @return string
	 */
	public function getCachedPackagesPath()
	{
		return $this->storagePath() . '/framework/packages.php';
	}

	/**
	 * Get cached services file path.
	 * @return string
	 */
	public function getCachedServicesPath()
	{
		return $this->storagePath() . '/framework/services.php';
	}

	/**
	 * Get the path to the cached packages.php file.
	 * @return string
	 */
	public function getCachedClassesPath()
	{
		return $this->storagePath() . '/framework/classes.php';
	}

	/*
	|--------------------------------------------------------------------------
	| Poppy framework Config Path
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get poppy framework path or assigned path.
	 * @param string $path
	 * @return string
	 */
	public function frameworkPath($path = '')
	{
		return realpath(__DIR__ . '/../../../framework' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
	}

	/**
	 * Get poppy module path.
	 * @return string
	 */
	public function modulePath()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'modules';
	}

	/**
	 * 设置插件路径
	 * @param  string $path
	 * @return $this
	 */
	public function setAddonPath($path)
	{
		$this->addonPath = $path;
		$this->instance('path.addon', $path);
		return $this;
	}

	/**
	 * @return string
	 */
	public function addonPath()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'addons';
	}


	/**
	 * 设置插件路径
	 * @param  string $path
	 * @return $this
	 */
	public function setExtensionPath($path)
	{
		$this->addonPath = $path;
		$this->instance('path.extension', $path);
		return $this;
	}

	/**
	 * @return string
	 */
	public function extensionPath()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'extensions';
	}


}

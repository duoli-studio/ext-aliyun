<?php namespace Poppy\Framework\Foundation;

use Closure;
use Exception;
use Illuminate\Foundation\Application as ApplicationBase;
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

	protected $pluginsPath;

	protected $modulesPath;


	/**
	 * 绑定路径到 container
	 * @return void
	 */
	protected function bindPathsInContainer()
	{
		parent::bindPathsInContainer();

		$this->instance('path.framework', $this->frameworkPath());
		$this->instance('path.module', $this->modulePath());
		$this->instance('path.extension', $this->addonPath());
		$this->instance('path.plugins', $this->pluginsPath());
	}

	/**
	 * 获取插件路径
	 * @return string
	 */
	public function pluginsPath()
	{
		return $this->pluginsPath ?: $this->basePath . '/plugins';
	}

	/**
	 * 设置插件路径
	 * @param  string $path
	 * @return $this
	 */
	public function setPluginsPath($path)
	{
		$this->pluginsPath = $path;
		$this->instance('path.plugins', $path);
		return $this;
	}

	/**
	 * 获取主题的路径
	 * @return string
	 */
	public function themesPath()
	{
		return $this->themesPath ?: $this->basePath . '/themes';
	}

	/**
	 * 设置皮肤的路径
	 * @param  string $path
	 * @return $this
	 */
	public function setThemesPath($path)
	{
		$this->themesPath = $path;
		$this->instance('path.themes', $path);
		return $this;
	}

	/**
	 * 获取临时文件路径
	 * @return string
	 */
	public function tempPath()
	{
		return $this->basePath . '/storage/temp';
	}

	/**
	 * Resolve the given type from the container.
	 * (Overriding Container::make)
	 * @param string $abstract
	 * @param array  $parameters
	 * @return mixed
	 */
	public function make($abstract, array $parameters = [])
	{
		$abstract = $this->getAlias($abstract);

		if (isset($this->deferredServices[$abstract])) {
			$this->loadDeferredProvider($abstract);
		}

		if ($parameters) {
			return $this->make(Maker::class)->make($abstract, $parameters);
		}

		return parent::make($abstract);
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
		} catch (Exception $ex) {
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
		return __DIR__ . '/../../../framework' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
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
	 * @return string
	 */
	public function addonPath()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'addons';
	}


	/**
	 * Get the path to the cached packages.php file.
	 * @return string
	 */
	public function getCachedClassesPath()
	{
		return $this->storagePath() . '/framework/classes.php';
	}
}

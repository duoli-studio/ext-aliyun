<?php namespace Poppy\Framework\Classes\Traits;

use Illuminate\Auth\AuthManager;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\TaggableStore;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Redis\RedisManager;
use Illuminate\Routing\Redirector;
use Illuminate\Session\SessionManager;
use Illuminate\View\Factory;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use Poppy\Framework\Config\Repository;
use Poppy\Framework\Foundation\Application;
use Poppy\Framework\Module\ModuleManager;
use Poppy\Framework\Extension\ExtensionManager;
use Poppy\Framework\Addon\AddonManager;
use Poppy\Framework\Poppy\Poppy;
use Poppy\Framework\Translation\Translator;
use Psr\Log\LoggerInterface;
use System\Setting\Repository\SettingRepository;

trait PoppyTrait
{


	/**
	 * @return AuthManager
	 */
	protected function getAuth(): AuthManager
	{
		return $this->getContainer()->make('auth');
	}


	/**
	 * @return Translator
	 */
	protected function getTranslator(): Translator
	{
		return $this->getContainer()->make('translator');
	}

	/**
	 * Get configuration instance.
	 * @return Repository
	 */
	protected function getConfig()
	{
		return $this->getContainer()->make('config');
	}

	/**
	 * @return DatabaseManager
	 */
	protected function getDb(): DatabaseManager
	{
		return $this->getContainer()->make('db');
	}

	/**
	 * Get console instance.
	 * @return \Illuminate\Contracts\Console\Kernel
	 */
	protected function getConsole()
	{
		$kernel = $this->getContainer()->make(Kernel::class);
		$kernel->bootstrap();

		return $kernel->getArtisan();
	}

	/**
	 * Get IoC Container.
	 * @return \Illuminate\Container\Container | Application
	 */
	protected function getContainer(): Container
	{
		return Container::getInstance();
	}

	/**
	 * Get mailer instance.
	 * @return Mailer
	 */
	protected function getMailer(): Mailer
	{
		return $this->getContainer()->make('mailer');
	}

	/**
	 * Get mailer instance.
	 * @return ModuleManager
	 */
	protected function getModule(): ModuleManager
	{
		return $this->getContainer()->make('module');
	}


	/**
	 * Get session instance.
	 * @return SessionManager
	 */
	protected function getSession(): SessionManager
	{
		return $this->getContainer()->make('session');
	}

	/**
	 * @return Request
	 */
	protected function getRequest(): Request
	{
		return $this->getContainer()->make('request');
	}

	/**
	 * @return Redirector
	 */
	protected function getRedirector(): Redirector
	{
		return $this->getContainer()->make('redirect');
	}

	/**
	 * @return \Illuminate\Events\Dispatcher
	 */
	protected function getEvent(): Dispatcher
	{
		return $this->getContainer()->make('events');
	}

	/**
	 * @return \Psr\Log\LoggerInterface
	 */
	protected function getLogger(): LoggerInterface
	{
		return $this->getContainer()->make('log');
	}

	/**
	 * @return \Illuminate\Contracts\Routing\ResponseFactory
	 */
	protected function getResponse()
	{
		return $this->getContainer()->make(ResponseFactory::class);
	}


	/**
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	protected function getFile()
	{
		return $this->getContainer()->make('files');
	}

	/**
	 * @return \Illuminate\Routing\UrlGenerator
	 */
	protected function getUrl()
	{
		return $this->getContainer()->make('url');
	}


	/**
	 * @return mixed|\Poppy\Framework\GraphQL\GraphQL
	 */
	protected function getGraphQL()
	{
		return $this->getContainer()->make('graphql');
	}

	/**
	 * @param string $tag
	 * @return \Illuminate\Cache\CacheManager
	 */
	protected function getCache($tag = ''): CacheManager
	{
		$cache = $this->getContainer()->make('cache');
		if ($cache instanceof TaggableStore && $tag) {
			$cache->tags($tag);
		}
		return $cache;
	}

	/**
	 * @return \Illuminate\Redis\RedisManager
	 */
	protected function getRedis(): RedisManager
	{
		return $this->getContainer()->make('redis');
	}

	/**
	 * @return Poppy
	 */
	protected function getPoppy(): Poppy
	{
		return $this->getContainer()->make('poppy');
	}

	/**
	 * @return \Illuminate\View\Factory
	 */
	protected function getView(): Factory
	{
		return $this->getContainer()->make('view');
	}

	/**
	 * @return ExtensionManager
	 */
	protected function getExtension(): ExtensionManager
	{
		return $this->getContainer()->make('extension');
	}

	/**
	 * @return AddonManager
	 */
	protected function getAddon(): AddonManager
	{
		return $this->getContainer()->make('addon');
	}


	/**
	 * Publish the file to the given path.
	 * @param string $from
	 * @param string $to
	 */
	protected function publishFile($from, $to)
	{
		$this->createParentDirectory(dirname($to));
		$this->getFile()->copy($from, $to);
	}

	/**
	 * Create the directory to house the published files if needed.
	 * @param $directory
	 */
	protected function createParentDirectory($directory)
	{
		if (!$this->getFile()->isDirectory($directory)) {
			$this->getFile()->makeDirectory($directory, 0755, true);
		}
	}

	/**
	 * Publish the directory to the given directory.
	 * @param $from
	 * @param $to
	 */
	protected function publishDirectory($from, $to)
	{
		$manager = new MountManager([
			'from' => new Flysystem(new LocalAdapter($from)),
			'to'   => new Flysystem(new LocalAdapter($to)),
		]);
		foreach ($manager->listContents('from://', true) as $file) {
			if ($file['type'] === 'file') {
				$manager->put('to://' . $file['path'], $manager->read('from://' . $file['path']));
			}
		}
	}
}



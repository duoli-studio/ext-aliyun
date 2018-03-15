<?php
namespace System\Extension;

use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Classes\Traits\PoppyTrait;

/**
 * Class ExpandServiceProvider.
 */
class ExtensionServiceProvider extends ServiceProvider
{
	use PoppyTrait;
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * @return array
	 */
	public function provides()
	{
		return ['extension'];
	}

	/**
	 * Register service provider.
	 */
	public function register()
	{
		$this->app->singleton('extension', function ($app) {
			return new ExtensionManager();
		});
	}
}

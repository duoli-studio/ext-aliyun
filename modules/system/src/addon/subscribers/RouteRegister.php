<?php namespace System\Addon\Subscribers;

use System\Addon\Controllers\AddonsController;
use System\Addon\Controllers\AddonsExportsController;
use System\Addon\Controllers\AddonsImportsController;

use \Poppy\Framework\Router\Abstracts\RouteRegister as AbstractRouteRegister;

/**
 * Class RouteRegister.
 */
class RouteRegister extends AbstractRouteRegister
{
	/**
	 * Handle Route Register.
	 */
	public function handle()
	{
		$this->router->group(['middleware' => ['auth:api', 'cross', 'web'], 'prefix' => 'api/administration'], function () {
			$this->router->resource('addons/{addon}/exports', AddonsExportsController::class)->methods([
				'store' => 'export',
			])->names([
				'store' => 'addons.exports',
			])->only([
				'store',
			]);
			$this->router->resource('addons/{addon}/imports', AddonsImportsController::class)->methods([
				'store' => 'import',
			])->names([
				'store' => 'addons.imports',
			])->only([
				'store',
			]);
			$this->router->resource('addons', AddonsController::class)->methods([
				'destroy' => 'uninstall',
				'index'   => 'list',
				'store'   => 'install',
				'update'  => 'enable',
			])->names([
				'destroy' => 'addons.uninstall',
				'index'   => 'addons.list',
				'store'   => 'addons.install',
				'update'  => 'addons.enable',
			])->only([
				'destroy',
				'index',
				'store',
				'update',
			]);
		});
	}
}

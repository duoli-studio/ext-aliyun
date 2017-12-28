<?php namespace Develop;

/**
 * Copyright (C) Update For IDE
 */

use Develop\Request\RouteServiceProvider;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{
	private $name = 'develop';

	/**
	 * Bootstrap the module services.
	 * @return void
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		parent::boot($this->name);
	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->app->register(RouteServiceProvider::class);
	}
}

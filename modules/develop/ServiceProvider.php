<?php namespace Develop;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Support\ModuleServiceProvider as ModuleServiceProviderBase;

class ServiceProvider extends ModuleServiceProviderBase
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot('develop');
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
	    parent::register('develop');
    }
}

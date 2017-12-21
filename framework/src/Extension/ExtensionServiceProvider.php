<?php

namespace Poppy\Framework\Extension;

use Poppy\Framework\Http\Abstracts\ServiceProvider;

/**
 * Class ExpandServiceProvider.
 */
class ExtensionServiceProvider extends ServiceProvider
{
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

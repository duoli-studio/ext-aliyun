<?php

namespace System\Extension\Subscribers;

use System\Extension\Controllers\ExtensionsController;
use Poppy\Framework\Routing\Abstracts\RouteRegister as AbstractRouteRegister;

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
        $this->router->group(['middleware' => ['auth:api_backend', 'cross', 'web'], 'prefix' => 'api/administration'], function () {
            $this->router->resource('extensions', ExtensionsController::class)->methods([
                'destroy' => 'uninstall',
                'store' => 'install',
            ])->names([
                'destroy' => 'addons.uninstall',
                'store' => 'extensions.install',
            ])->only([
                'destroy',
                'store',
            ]);
        });
    }
}

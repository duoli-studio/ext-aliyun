<?php namespace Poppy\Framework\Addon\Abstracts;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

/**
 * Class Extension.
 */
abstract class Addon extends ServiceProvider
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected static $migrations;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Extension constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->events = $app['events'];
        $this->router = $app['router'];
        static::$migrations = new Collection();
    }

    /**
     * Installer for extension.
     *
     * @return string
     */
    abstract public static function install();

    /**
     * @return array
     */
    public static function migrations()
    {
        return static::$migrations->toArray();
    }

    /**
     * Uninstall for extension.
     *
     * @return string
     */
    abstract public static function uninstall();

    /**
     * Boot extension.
     */
    abstract public function boot();

    /**
     * @param array|string $paths
     */
    public function loadMigrationsFrom($paths)
    {
        static::$migrations = static::$migrations->merge((array)$paths);
        parent::loadMigrationsFrom($paths);
    }

    /**
     * Register extension extra providers.
     */
    public function register()
    {
    }
}

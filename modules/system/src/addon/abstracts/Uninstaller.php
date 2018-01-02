<?php namespace System\Addon\Abstracts;

use Illuminate\Container\Container;

/**
 * Class Uninstaller.
 */
abstract class Uninstaller
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Uninstaller constructor.
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return bool
	 */
	abstract public function handle();

	/**
	 * @return bool
	 */
	public abstract function require();

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		if (!$this->require()) {
			return false;
		}

		return $this->handle();
	}
}

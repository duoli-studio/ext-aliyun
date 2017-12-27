<?php namespace System\Extension;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Extension\Repositories\Extensions;

/**
 * Class ExtensionManager.
 */
class ExtensionManager
{
	use PoppyTrait;

	/**
	 * @var Extensions
	 */
	protected $repository;

	public function register()
	{
		$this->repository()->each(function (Extension $extension) {
			if (isset($extension['service']) && $extension['service']) {
				$this->getContainer()->register($extension->service());
			}
		});
	}

	/**
	 * @param $identification
	 * @return bool
	 */
	public function has($identification)
	{
		return $this->repository()->has($identification);
	}

	/**
	 * @return Extensions
	 */
	public function repository()
	{
		if (!$this->repository instanceof Extensions) {
			$this->repository = new Extensions();
			$this->repository->initialize(collect($this->getFile()->directories($this->getExtensionPath())));
		}

		return $this->repository;
	}

	/**
	 * @return string
	 */
	protected function getExtensionPath(): string
	{
		return $this->getContainer()->extensionPath();
	}
}

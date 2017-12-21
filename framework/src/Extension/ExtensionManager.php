<?php namespace Poppy\Framework\Extension;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Extension\Repositories\ExtensionRepository;

/**
 * Class ExtensionManager.
 */
class ExtensionManager
{
	use PoppyTrait;

	/**
	 * @var ExtensionRepository
	 */
	protected $repository;

	/**
	 * @param $identification
	 * @return bool
	 */
	public function has($identification)
	{
		return $this->repository()->has($identification);
	}

	/**
	 * @return ExtensionRepository
	 */
	public function repository()
	{
		if (!$this->repository instanceof ExtensionRepository) {
			$this->repository = new ExtensionRepository();
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

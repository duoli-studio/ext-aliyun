<?php namespace System\Extension;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Extension\Repositories\Extensions;
use System\Extension\Repositories\Navigations;

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


	protected $navigation;

	public function register()
	{
		$this->repository()->each(function (Extension $extension) {
			if (isset($extension['service']) && $extension['service'] && class_exists($extension['service'])) {
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
	 * @return Navigations
	 */
	public function navigations()
	{
		if (!$this->navigation instanceof Navigations) {
			$collection       = $this->enabled()->map(function (Extension $addon) {
				return $addon->offsetExists('navigations') ? (array) $addon->get('navigations') : [];
			});
			$this->navigation = new Navigations();
			$this->navigation->initialize($collection);
		}
		return $this->navigation;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function enabled(): Collection
	{
		return $this->repository()->enabled();
	}

	/**
	 * @return string
	 */
	protected function getExtensionPath(): string
	{
		return $this->getContainer()->extensionPath();
	}
}

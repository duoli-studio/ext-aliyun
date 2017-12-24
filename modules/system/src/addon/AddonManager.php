<?php namespace System\Addon;

use Illuminate\Support\Collection;
use System\Addon\Repositories\AddonRepository;
use System\Addon\Repositories\AssetsRepository;
use System\Addon\Repositories\NavigationRepository;
use Poppy\Framework\Classes\Traits\PoppyTrait;

/**
 * Class ExtensionManager.
 */
class AddonManager
{
	use PoppyTrait;

	/**
	 * @var AssetsRepository
	 */
	protected $assetsRepository;

	/**
	 * @var \Illuminate\Support\Collection
	 */
	protected $excepts;

	/**
	 * @var NavigationRepository
	 */
	protected $navigationRepository;

	/**
	 * @var AddonRepository
	 */
	protected $repository;

	/**
	 * AddonManager constructor.
	 */
	public function __construct()
	{
		$this->excepts = collect();
	}

	/**
	 * Get a extension by name.
	 * @param $name
	 * @return Addon
	 */
	public function get($name): Addon
	{
		return $this->repository()->get($name);
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function enabled(): Collection
	{
		return $this->repository()->enabled();
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function installed(): Collection
	{
		return $this->repository()->installed();
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function notInstalled(): Collection
	{
		return $this->repository()->notInstalled();
	}

	/**
	 * @return NavigationRepository
	 */
	public function navigations()
	{
		if (!$this->navigationRepository instanceof NavigationRepository) {
			$collection                 = $this->enabled()->map(function (Addon $addon) {
				return $addon->offsetExists('navigations') ? (array) $addon->get('navigations') : [];
			});
			$this->navigationRepository = new NavigationRepository();
			$this->navigationRepository->initialize($collection);
		}
		return $this->navigationRepository;
	}

	/**
	 * @return AddonRepository
	 */
	public function repository(): AddonRepository
	{
		if (!$this->repository instanceof AddonRepository) {
			$collection = collect();
			collect($this->getFile()->directories($this->getExtensionPath()))->each(function ($vendor) use ($collection) {
				collect($this->getFile()->directories($vendor))->each(function ($directory) use ($collection) {
					$collection->push($directory);
				});
			});
			$this->repository = new AddonRepository();
			$this->repository->initialize($collection);
		}

		return $this->repository;
	}

	/**
	 * Path for extension.
	 * @return string
	 */
	public function getExtensionPath(): string
	{
		return $this->getContainer()->addonPath();
	}

	/**
	 * Check for extension exist.
	 * @param $name
	 * @return bool
	 */
	public function has($name): bool
	{
		return $this->repository()->has($name);
	}

	/**
	 * @return AssetsRepository
	 */
	public function assets()
	{
		if (!$this->assetsRepository instanceof AssetsRepository) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Addon $addon) use ($collection) {
				$collection->put($addon->identification(), $addon->get('assets', []));
			});
			$this->assetsRepository = new AssetsRepository();
			$this->assetsRepository->initialize($collection);
		}

		return $this->assetsRepository;
	}

	/**
	 * Vendor Path.
	 * @return string
	 */
	public function getVendorPath(): string
	{
		return $this->getContainer()->basePath() . DIRECTORY_SEPARATOR . 'vendor';
	}

	/**
	 * @return array
	 */
	public function getExcepts()
	{
		return $this->excepts->toArray();
	}

	/**
	 * @param $excepts
	 */
	public function registerExcept($excepts)
	{
		foreach ((array) $excepts as $except) {
			$this->excepts->push($except);
		}
	}
}

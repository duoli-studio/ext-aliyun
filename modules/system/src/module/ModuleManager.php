<?php namespace System\Module;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Module\Repositories\AssetsRepository;
use System\Module\Repositories\MenuRepository;
use System\Module\Repositories\ModuleRepository;
use System\Module\Repositories\PageRepository;

/**
 * Class ModuleManager.
 */
class ModuleManager
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
	 * @var MenuRepository
	 */
	protected $menuRepository;

	/**
	 * @var PageRepository
	 */
	protected $pageRepository;

	/**
	 * @var ModuleRepository
	 */
	protected $repository;

	/**
	 * ModuleManager constructor.
	 */
	public function __construct()
	{
		$this->excepts = collect();
	}

	/**
	 * @return Collection
	 */
	public function enabled()
	{
		return $this->repository()->enabled();
	}

	/**
	 * @return ModuleRepository
	 */
	public function repository(): ModuleRepository
	{
		if (!$this->repository instanceof ModuleRepository) {
			$this->repository = new ModuleRepository();
			$slugs            = app('poppy')->enabled()->pluck('slug');
			$this->repository->initialize($slugs);
		}
		return $this->repository;
	}

	/**
	 * Get a module by name.
	 * @param $name
	 * @return Module
	 */
	public function get($name): Module
	{
		return $this->repository->get($name);
	}

	/**
	 * Check for module exist.
	 * @param $name
	 * @return bool
	 */
	public function has($name): bool
	{
		return $this->repository->has($name);
	}

	/**
	 * @return array
	 */
	public function getExcepts()
	{
		return $this->excepts->toArray();
	}

	/**
	 * @return MenuRepository
	 */
	public function menus(): MenuRepository
	{
		if (!$this->menuRepository instanceof MenuRepository) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('menus', []));
			});
			$this->menuRepository = new MenuRepository();
			$this->menuRepository->initialize($collection);
		}

		return $this->menuRepository;
	}

	/**
	 * @return PageRepository
	 */
	public function pages(): PageRepository
	{
		if (!$this->pageRepository instanceof PageRepository) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('pages', []));
			});
			$this->pageRepository = new PageRepository();
			$this->pageRepository->initialize($collection);
		}

		return $this->pageRepository;
	}

	/**
	 * @return AssetsRepository
	 */
	public function assets(): AssetsRepository
	{
		if (!$this->assetsRepository instanceof AssetsRepository) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('assets', []));
			});
			$this->assetsRepository = new AssetsRepository();
			$this->assetsRepository->initialize($collection);
		}

		return $this->assetsRepository;
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

<?php namespace System\Module;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Module\Repositories\ModulesAssets;
use System\Module\Repositories\ModulesMenu;
use System\Module\Repositories\Modules;
use System\Module\Repositories\ModulesPage;

/**
 * Class ModuleManager.
 */
class ModuleManager
{
	use PoppyTrait;

	/**
	 * @var ModulesAssets
	 */
	protected $assetsRepository;

	/**
	 * @var \Illuminate\Support\Collection
	 */
	protected $excepts;

	/**
	 * @var ModulesMenu
	 */
	protected $menuRepository;

	/**
	 * @var ModulesPage
	 */
	protected $pageRepository;

	/**
	 * @var Modules
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
	 * @return Modules
	 */
	public function repository(): Modules
	{
		if (!$this->repository instanceof Modules) {
			$this->repository = new Modules();
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
		return $this->repository()->get($name);
	}

	/**
	 * Check for module exist.
	 * @param $name
	 * @return bool
	 */
	public function has($name): bool
	{
		return $this->repository()->has($name);
	}

	/**
	 * @return array
	 */
	public function getExcepts()
	{
		return $this->excepts->toArray();
	}

	/**
	 * @return ModulesMenu
	 */
	public function menus(): ModulesMenu
	{
		if (!$this->menuRepository instanceof ModulesMenu) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('menus', []));
			});
			$this->menuRepository = new ModulesMenu();
			$this->menuRepository->initialize($collection);
		}

		return $this->menuRepository;
	}

	/**
	 * @return ModulesPage
	 */
	public function pages(): ModulesPage
	{
		if (!$this->pageRepository instanceof ModulesPage) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('pages', []));
			});
			$this->pageRepository = new ModulesPage();
			$this->pageRepository->initialize($collection);
		}

		return $this->pageRepository;
	}

	/**
	 * @return ModulesAssets
	 */
	public function assets(): ModulesAssets
	{
		if (!$this->assetsRepository instanceof ModulesAssets) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('assets', []));
			});
			$this->assetsRepository = new ModulesAssets();
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

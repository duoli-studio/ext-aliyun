<?php namespace System\Module;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Module\Repositories\ModulesAssets;
use System\Module\Repositories\ModulesBackendMenu;
use System\Module\Repositories\ModulesMenu;
use System\Module\Repositories\Modules;
use System\Module\Repositories\ModulesDevelopMenu;
use System\Module\Repositories\ModulesPage;
use System\Module\Repositories\ModulesSetting;

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
	 * @var ModulesSetting
	 */
	protected $settingRepository;

	/**
	 * @var ModulesDevelopMenu
	 */
	protected $developMenus;

	/**
	 * @var ModulesBackendMenu
	 */
	protected $backendMenus;

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
	 * @return ModulesSetting
	 */
	public function settings(): ModulesSetting
	{
		if (!$this->settingRepository instanceof ModulesSetting) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('settings', []));
			});
			$this->settingRepository = new ModulesSetting();
			$this->settingRepository->initialize($collection);
		}

		return $this->settingRepository;
	}

	/**
	 * @return ModulesDevelopMenu
	 */
	public function developMenus(): ModulesDevelopMenu
	{
		if (!$this->developMenus instanceof ModulesDevelopMenu) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('develop_menus', []));
			});
			$this->developMenus = new ModulesDevelopMenu();
			$this->developMenus->initialize($collection);
		}
		return $this->developMenus;
	}

	/**
	 * @return ModulesBackendMenu
	 */
	public function backendMenus(): ModulesBackendMenu
	{
		if (!$this->backendMenus instanceof ModulesBackendMenu) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('backend_menus', []));
			});
			$this->backendMenus = new ModulesBackendMenu();
			$this->backendMenus->initialize($collection);
		}
		return $this->backendMenus;
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

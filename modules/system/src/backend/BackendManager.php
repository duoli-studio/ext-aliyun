<?php namespace System\Backend;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use System\Backend\Abstracts\Backend;
use System\Backend\Repositories\NavigationRepository;
use System\Backend\Repositories\PageRepository;
use System\Backend\Repositories\ScriptRepository;
use System\Backend\Repositories\StylesheetRepository;
use System\Classes\Traits\SystemTrait;

/**
 * Class AdministrationManager.
 */
class BackendManager
{
	use SystemTrait;

	/**
	 * @var Backend
	 */
	protected $backend;

	/**
	 * @var NavigationRepository
	 */
	protected $navigationRepository;

	/**
	 * @var PageRepository
	 */
	protected $pageRepository;

	/**
	 * @var ScriptRepository
	 */
	protected $scriptRepository;

	/**
	 * @var StylesheetRepository
	 */
	protected $stylesheetRepository;

	/**
	 * Get administrator.
	 * @return Backend
	 */
	public function getBackend()
	{
		return $this->backend;
	}

	/**
	 * Status of administrator's instance.
	 * @return bool
	 */
	public function hasBackend()
	{
		return is_null($this->backend) ? false : true;
	}

	/**
	 * Set backend instance.
	 * @param Backend $backend
	 */
	public function setBackend(Backend $backend)
	{
		if (is_object($this->backend)) {
			throw new InvalidArgumentException('Administrator has been Registered!');
		}
		if ($backend instanceof Backend) {
			$this->backend = $backend;
			$this->backend->init();
		}
		else {
			throw new InvalidArgumentException('Administrator must be instanceof ' . Backend::class . '!');
		}
	}

	/**
	 * @return NavigationRepository
	 */
	public function navigations()
	{
		if (!$this->navigationRepository instanceof NavigationRepository) {
			$this->navigationRepository = new NavigationRepository();
			$this->navigationRepository->initialize($this->getModule()->menus()->structures());
		}

		return $this->navigationRepository;
	}

	/**
	 * @return Collection
	 */
	public function pages()
	{
		if (!$this->pageRepository instanceof PageRepository) {
			$this->pageRepository = new PageRepository();
			$this->pageRepository->initialize(collect());
		}
		return $this->pageRepository;
	}

	/**
	 * @return ScriptRepository
	 */
	public function scripts()
	{
		if (!$this->scriptRepository instanceof ScriptRepository) {
			$this->scriptRepository = new ScriptRepository();
			$this->scriptRepository->initialize(collect());
		}

		return $this->scriptRepository;
	}

	/**
	 * @return StylesheetRepository
	 */
	public function stylesheets()
	{
		if (!$this->stylesheetRepository instanceof StylesheetRepository) {
			$this->stylesheetRepository = new StylesheetRepository();
			$this->stylesheetRepository->initialize(collect());
		}
		return $this->stylesheetRepository;
	}
}

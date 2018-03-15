<?php namespace System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Exceptions\LoadConfigurationException;
use Poppy\Framework\Support\Abstracts\Repository;
use Symfony\Component\Yaml\Yaml;
use System\Classes\Traits\SystemTrait;
use System\Module\Module;

/**
 * Class ModuleRepository.
 */
class Modules extends Repository
{
	use SystemTrait;
	/**
	 * @var bool
	 */
	protected $loadFromCache = true;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $slugs
	 */
	public function initialize(Collection $slugs)
	{
		$this->items = $this->getCache('poppy')->rememberForever(
			'modules',
			function () use ($slugs) {
			// load from file
			$this->loadFromCache = false;
			$collection          = collect();
			$slugs->each(function ($slug) use ($collection) {
				$module = new Module($slug);
				if ($this->getFile()->exists($file = $module->directory() . DIRECTORY_SEPARATOR . 'manifest.json')) {
					// load config
					$configurations = $this->loadConfigurations($module->directory());

					// set config to module
					$configurations->isNotEmpty() && $configurations->each(function ($value, $item) use ($module) {
						$module->offsetSet($item, $value);
					});

					// is enable
					$module->offsetSet('enabled', $module->isEnabled());

					// put all config to repository use key `slug`
					$collection->put($slug, $module);
				}
			});

			return $collection->all();
		}
		);
	}

	/**
	 * Load configuration from module configurations folder.
	 * @param string $directory
	 * @return Collection
	 * @throws \Exception
	 */
	protected function loadConfigurations(string $directory)
	{
		$directory = $directory . DIRECTORY_SEPARATOR . 'configurations';
		if ($this->getFile()->isDirectory($directory)) {
			$configurations = collect();

			// load module, in root element
			$module = $directory . DIRECTORY_SEPARATOR . 'module.yaml';
			if ($this->getFile()->exists($module)) {
				$configurations = collect(Yaml::parse($this->getFile()->get($module)));
			}

			// load other config except module.yaml file
			// put it in filename key
			collect($this->getFile()->files($directory))->each(function ($file) use ($configurations) {
				$name = basename(realpath($file), '.yaml');
				if ($this->getFile()->isReadable($file) && $name !== 'module') {
					$configurations->put($name, Yaml::parse(file_get_contents($file)));
				}
			});

			return $configurations;
		}
		 
			throw new LoadConfigurationException('Load Module fail: ' . $directory);
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function enabled(): Collection
	{
		return $this->filter(function (Module $module) {
			return $module->get('enabled') == true;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function loaded(): Collection
	{
		return $this->filter(function (Module $module) {
			return $module->get('initialized') == true;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function notLoaded(): Collection
	{
		return $this->filter(function (Module $module) {
			return $module->get('initialized') == false;
		});
	}
}

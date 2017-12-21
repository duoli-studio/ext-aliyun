<?php namespace Poppy\Framework\Module\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Abstracts\CollectRepository;
use Poppy\Framework\Module\Module;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ModuleRepository.
 */
class ModuleRepository extends CollectRepository
{
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
		$cache = $this->getCache();
		if ($cache instanceof TaggableStore) {
			$cache->tags('poppy');
		}
		$this->items = $cache->rememberForever('module.repository', function () use ($slugs) {
			// load from file
			$this->loadFromCache = false;
			$collection          = collect();
			$slugs->each(function ($slug) use ($collection) {
				$module = new Module($slug);
				if ($this->getFile()->exists($file = $module->directory() . DIRECTORY_SEPARATOR . 'manifest.json')) {
					$configurations = $this->loadConfigurations($module->directory());
					$configurations->isNotEmpty() && $configurations->each(function ($value, $item) use ($module) {
						$module->offsetSet($item, $value);
					});

					$module->offsetSet('enabled', $module->isEnabled());
					$collection->put($configurations->get('slug'), $module);
				}
			});
			return $collection->all();
		});
	}

	/**
	 * @param string $directory
	 * @return \Illuminate\Support\Collection
	 * @throws \Exception
	 */
	protected function loadConfigurations(string $directory)
	{
		if ($this->getFile()->exists($file = $directory . DIRECTORY_SEPARATOR . 'configuration.yaml')) {
			return collect(Yaml::parse(file_get_contents($file)));
		}
		else {
			$directory = $directory . DIRECTORY_SEPARATOR . 'configurations';
			if ($this->getFile()->isDirectory($directory)) {
				$module = $directory . DIRECTORY_SEPARATOR . 'module.yaml';
				if ($this->getFile()->exists($module)) {
					$configurations = collect(Yaml::parse($this->getFile()->get($module)));
				}
				else {
					$configurations = collect();
				}
				collect($this->getFile()->files($directory))->each(function ($file) use ($configurations) {
					$name = basename(realpath($file), '.yaml');
					if ($this->getFile()->isReadable($file) && $name !== 'module') {
						$configurations->put($name, Yaml::parse(file_get_contents($file)));
					}
				});

				return $configurations;
			}
			else {
				throw new \Exception('Load Module fail: ' . $directory);
			}
		}
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

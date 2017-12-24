<?php

namespace System\Addon\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use Symfony\Component\Yaml\Yaml;
use System\Addon\Addon;
use System\Classes\Traits\SystemTrait;

/**
 * Class ExtensionRepository.
 */
class AddonRepository extends Repository
{
	use SystemTrait;
	/**
	 * @var bool
	 */
	protected $loadFromCache = true;

	/**
	 * Initialize.
	 * @param \Illuminate\Support\Collection $data
	 */
	public function initialize(Collection $data)
	{
		$this->items = $this->getCache('poppy')->rememberForever('addon.repository', function () use ($data) {
			$collection = collect();
			$data->each(function ($directory) use ($collection) {
				$addon = new Addon([
					'directory' => $directory,
				]);
				if ($this->getFile()->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
					$package        = collect(json_decode($this->getFile()->get($file), true));
					$configurations = $this->loadConfigurations($directory);
					$configurations->isNotEmpty() && $configurations->each(function ($value, $item) use ($addon) {
						$addon->offsetSet($item, $value);
					});
					if ($package->get('type') == 'poppy-addon'
						&& $configurations->get('identification') == $package->get('name')
						&& $addon->validate()) {
						$autoload = collect([
							$directory,
							'vendor',
							'autoload.php',
						])->implode(DIRECTORY_SEPARATOR);
						if ($this->getFile()->exists($autoload)) {
							$addon->offsetSet('autoload', $autoload);
							$this->getFile()->requireOnce($autoload);
							$this->loadFromCache = false;
						}
						if (!$addon->offsetExists('provider')) {
							collect(data_get($package, 'autoload.psr-4'))->each(function ($entry, $namespace) use ($addon) {
								$addon->offsetSet('namespace', $namespace);
								$addon->offsetSet('provider', $namespace . 'Extension');
							});
						}
						$provider = $addon->offsetGet('provider');
						$addon->offsetSet('initialized', boolval(class_exists($provider) ?: false));
						$key = 'addon.' . $addon->offsetGet('identification') . '.enabled';
						$addon->offsetSet('enabled', $this->getSetting()->get($key, false));
						$key = 'addon.' . $addon->offsetGet('identification') . '.installed';
						$addon->offsetSet('installed', $this->getSetting()->get($key, false));
					}
					$collection->put($configurations->get('identification'), $addon);
				}
			});

			return $collection->all();
		});
		if ($this->loadFromCache) {
			collect($this->items)->each(function (Addon $addon) {
				if ($addon->offsetExists('autoload')) {
					$autoload = $addon->get('autoload');
					$this->getFile()->exists($autoload) && $this->getFile()->requireOnce($autoload);
				}
			});
		}
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function enabled(): Collection
	{
		return $this->filter(function (Addon $addon) {
			return $addon->get('enabled') == true;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function installed(): Collection
	{
		return $this->filter(function (Addon $addon) {
			return $addon->get('installed') == true;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function notInstalled(): Collection
	{
		return $this->filter(function (Addon $addon) {
			return $addon->get('installed') == false;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function loaded(): Collection
	{
		return $this->filter(function (Addon $addon) {
			return $addon->get('initialized') == true;
		});
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function notLoaded(): Collection
	{
		return $this->filter(function (Addon $addon) {
			return $addon->get('initialized') == false;
		});
	}

	/**
	 * @param string $directory
	 * @return \Illuminate\Support\Collection
	 * @throws \Exception
	 */
	protected function loadConfigurations(string $directory): Collection
	{
		if ($this->getFile()->exists($file = $directory . DIRECTORY_SEPARATOR . 'configuration.yaml')) {
			return collect(Yaml::parse(file_get_contents($file)));
		}
		else {
			if ($this->getFile()->isDirectory($directory = $directory . DIRECTORY_SEPARATOR . 'configurations')) {
				$configurations = collect();
				collect($this->getFile()->files($directory))->each(function ($file) use ($configurations) {
					if ($this->getFile()->isReadable($file)) {
						collect(Yaml::dump(file_get_contents($file)))->each(function ($data, $key) use ($configurations) {
							$configurations->put($key, $data);
						});
					}
				});

				return $configurations;
			}
			else {
				throw new \Exception('Load Extension fail: ' . $directory);
			}
		}
	}
}
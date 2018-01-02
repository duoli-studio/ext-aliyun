<?php namespace System\Extension\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use Symfony\Component\Yaml\Yaml;
use System\Classes\Traits\SystemTrait;
use System\Extension\Extension;

/**
 * Class ExpandRepository.
 */
class Extensions extends Repository
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

		$this->items = $this->getCache('poppy')->rememberForever(
			'extensions', function () use ($data) {
			$collection = collect();
			$data->each(function ($directory, $index) use ($collection) {
				$extension = new Extension([
					'directory' => $directory,
				]);
				if ($this->getFile()->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {

					$configurations = $this->loadConfigurations($directory);
					$configurations->isNotEmpty() && $configurations->each(function ($value, $item) use ($extension) {
						$extension->offsetSet($item, $value);
					});

					$package        = collect(json_decode($this->getFile()->get($file), true));
					$identification = data_get($package, 'name');
					$extension->offsetSet('identification', $identification);
					$extension->offsetSet('description', data_get($package, 'description'));
					$extension->offsetSet('authors', data_get($package, 'authors'));
					if ($package->get('type') == 'poppy-extension' && $extension->validate()) {
						$autoload = collect([$directory, 'vendor', 'autoload.php'])->implode(DIRECTORY_SEPARATOR);
						if ($this->getFile()->exists($autoload)) {
							$extension->offsetSet('autoload', $autoload);
							$this->getFile()->requireOnce($autoload);
							$this->loadFromCache = false;
						}
						collect(data_get($package, 'autoload.psr-4'))->each(function ($entry, $namespace) use (
							$extension
						) {
							$extension->offsetSet('namespace', $namespace);
							$extension->offsetSet('service', $namespace . 'ExtensionServiceProvider');
						});

						// check provider exists to initialized
						$provider = $extension->offsetGet('service');
						$extension->offsetSet('initialized', boolval(class_exists($provider) ?: false));
						$key = 'extension.' . $identification . '.enabled';
						$extension->offsetSet('enabled', boolval($this->getSetting()->get($key, false)));
						$key = 'extension.' . $identification . '.installed';
						$extension->offsetSet('installed', boolval($this->getSetting()->get($key, false)));
						$install   = 'extension.' . $identification . '.require.install';
						$uninstall = 'extension.' . $identification . '.require.uninstall';
						$extension->offsetSet('require', [
							'install'   => boolval($this->getSetting()->get($install, false)),
							'uninstall' => boolval($this->getSetting()->get($uninstall, false)),
						]);
					}
					$collection->put($extension->get('identification'), $extension);
				}
			});

			return $collection->all();
		});
		if ($this->loadFromCache) {
			collect($this->items)->each(function (Extension $extension) {
				if ($extension->offsetExists('autoload')) {
					$autoload = $extension->get('autoload');
					$this->getFile()->exists($autoload) && $this->getFile()->requireOnce($autoload);
				}
			});
		}
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

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function enabled(): Collection
	{
		return $this->filter(function (Extension $extension) {
			return $extension->get('enabled') == true;
		});
	}
}

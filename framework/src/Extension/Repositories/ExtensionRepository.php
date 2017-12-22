<?php namespace Poppy\Framework\Extension\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Extension\Extension;
use Poppy\Framework\Support\Abstracts\Repository;
use System\Classes\Traits\SystemTrait;

/**
 * Class ExpandRepository.
 */
class ExtensionRepository extends Repository
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

		$this->items = $this->getCache('poppy')->rememberForever('extension.repository', function () use ($data) {
			$collection = collect();
			$data->each(function ($directory, $index) use ($collection) {
				$extension = new Extension([
					'directory' => $directory,
				]);
				if ($this->getFile()->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
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
}

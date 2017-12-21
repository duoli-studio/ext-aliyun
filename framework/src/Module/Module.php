<?php namespace Poppy\Framework\Module;

use ArrayAccess;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonSerializable;
use Poppy\Framework\Classes\Traits\HasAttributesTrait;

/**
 * Class Module.
 */
class Module implements Arrayable, ArrayAccess, JsonSerializable
{
	use HasAttributesTrait;

	/**
	 * Module constructor.
	 * @param $slug
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	public function __construct($slug)
	{
		$poppy            = app('poppy');
		$this->attributes = [
			'directory' => poppy_path($slug),
			'namespace' => poppy_class($slug),
			'slug'      => $slug,
			'enabled'   => $poppy->isEnabled($slug),
		];
	}

	/**
	 * @return string
	 */
	public function directory()
	{
		return $this->attributes['directory'];
	}

	/**
	 * @return string
	 */
	public function namespace()
	{
		return $this->attributes['namespace'];
	}

	/**
	 * @return string
	 */
	public function slug(): string
	{
		return $this->attributes['slug'];
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return boolval($this->attributes['enabled']);
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function pages(): Collection
	{
		return collect($this->get('pages', []))->map(function ($definition, $identification) {
			$definition['initialization']['identification'] = $identification;
			unset($definition['initialization']['tabs']);

			return $definition['initialization'];
		})->groupBy('target');
	}


	/**
	 * @param string $entry
	 * @return array
	 */
	public function scripts($entry): array
	{
		$data   = collect();
		$exists = collect(data_get($this->attributes, 'assets.' . $entry));
		$exists->isNotEmpty() && $exists->each(function ($definitions, $identification) use ($data) {
			if (isset($definitions['permissions']) && $definitions['permissions']) {
				if ($this->checkPermission($definitions['permissions'])) {
					$scripts = $definitions['scripts'];
				}
				else {
					$scripts = [];
				}
			}
			else {
				$scripts = $definitions['scripts'];
			}
			collect((array) $scripts)->each(function ($script) use ($data, $identification) {
				$data->put($identification, asset($script));
			});
		});

		return $data->toArray();
	}

	/**
	 * @param $identification
	 * @return bool
	 */
	protected function checkPermission($identification): bool
	{
		if (!$identification) {
			return true;
		}
		$user = Container::getInstance()->make(Factory::class)->guard('api')->user();
		if ((!$user instanceof Model) || (!Member::hasMacro('groups'))) {
			return false;
		}
		foreach (collect($user->load('groups')->getAttribute('groups'))->toArray() as $group) {
			if (Container::getInstance()->make(PermissionManager::class)->check($identification, $group['identification'])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $entry
	 * @return array
	 */
	public function stylesheets($entry): array
	{
		$data   = collect();
		$exists = collect(data_get($this->attributes, 'assets.' . $entry));
		$exists->isNotEmpty() && $exists->each(function ($definitions, $identification) use ($data) {
			if (isset($definitions['permissions']) && $definitions['permissions']) {
				if ($this->checkPermission($definitions['permissions'])) {
					$scripts = $definitions['stylesheets'];
				}
				else {
					$scripts = [];
				}
			}
			else {
				$scripts = $definitions['stylesheets'];
			}
			collect((array) $scripts)->each(function ($script) use ($data, $identification) {
				$data->put($identification, asset($script));
			});
		});

		return $data->toArray();
	}

	/**
	 * @return bool
	 */
	public function validate(): bool
	{
		return $this->offsetExists('name')
			&& $this->offsetExists('identification')
			&& $this->offsetExists('description')
			&& $this->offsetExists('authors');
	}
}

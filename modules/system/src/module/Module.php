<?php namespace System\Module;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;
use Poppy\Framework\Classes\Traits\HasAttributesTrait;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;

/**
 * Class Module.
 */
class Module implements Arrayable, ArrayAccess, JsonSerializable
{
	use HasAttributesTrait, SystemTrait;

	/**
	 * Module constructor.
	 * @param $slug
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	public function __construct($slug)
	{
		$this->attributes = [
			'directory' => poppy_path($slug),
			'namespace' => poppy_class($slug),
			'slug'      => $slug,
			'enabled'   => $this->getPoppy()->isEnabled($slug),
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
				if ($this->checkPermission($definitions['permissions'], PamAccount::GUARD_BACKEND)) {
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
	 * @param $guard
	 * @return bool
	 */
	protected function checkPermission($identification, $guard): bool
	{
		if (!$identification) {
			return true;
		}

		return $this->getPermission()->check($identification, $guard);
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
				if ($this->checkPermission($definitions['permissions'], PamAccount::GUARD_BACKEND)) {
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

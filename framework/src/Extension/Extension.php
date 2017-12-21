<?php namespace Poppy\Framework\Extension;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Poppy\Framework\Classes\Traits\HasAttributesTrait;

/**
 * Class Expand.
 */
class Extension implements Arrayable, ArrayAccess, JsonSerializable
{
	use HasAttributesTrait;

	/**
	 * Module constructor.
	 * @param array $attributes
	 */
	public function __construct(array $attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * @return string
	 */
	public function identification(): string
	{
		return $this->attributes['identification'];
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return boolval($this->attributes['enabled']);
	}

	/**
	 * @return bool
	 */
	public function isInstalled(): bool
	{
		return boolval($this->attributes['installed']);
	}

	/**
	 * @return string
	 */
	public function service(): string
	{
		return $this->attributes['service'];
	}

	/**
	 * check name, description and authors not empty
	 * @return bool
	 */
	public function validate(): bool
	{
		return $this->offsetExists('identification')
			&& $this->offsetExists('description')
			&& $this->offsetExists('authors');
	}
}

<?php

namespace Poppy\Framework\Addon\Events;

use Poppy\Framework\Addon\Addon;

/**
 * Class ExtensionEnabled.
 */
class AddonEnabled
{

	protected $extension;

	/**
	 * ExtensionEnabled constructor.
	 * @param Addon $extension
	 */
	public function __construct(Addon $extension)
	{
		$this->extension = $extension;
	}
}

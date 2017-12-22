<?php

namespace System\Addon\Events;

use System\Addon\Addon;

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

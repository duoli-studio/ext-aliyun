<?php

namespace Poppy\Framework\Extension\Abstracts;

/**
 * Class Uninstaller.
 */
abstract class Uninstaller
{
    /**
     * @return true
     */
    abstract public function handle();
}
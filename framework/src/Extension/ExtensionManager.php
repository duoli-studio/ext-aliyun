<?php

namespace Poppy\Framework\Extension;

use Poppy\Framework\Extension\Repositories\ExtensionRepository;
use Poppy\Framework\Routing\Traits\Helpers;

/**
 * Class ExtensionManager.
 */
class ExtensionManager
{
    use Helpers;

    /**
     * @var \Poppy\Framework\Extension\Repositories\ExtensionRepository
     */
    protected $repository;

    /**
     * @param $identification
     *
     * @return bool
     */
    public function has($identification)
    {
        return $this->repository()->has($identification);
    }

    /**
     * @return \Poppy\Framework\Extension\Repositories\ExtensionRepository
     */
    public function repository()
    {
        if (!$this->repository instanceof ExtensionRepository) {
            $this->repository = new ExtensionRepository();
            $this->repository->initialize(collect($this->file->directories($this->getExtensionPath())));
        }

        return $this->repository;
    }

    /**
     * @return string
     */
    protected function getExtensionPath(): string
    {
        return $this->container->extensionPath();
    }
}

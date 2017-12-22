<?php
namespace System\Addon\Handlers;

use Illuminate\Container\Container;
use System\Addon\AddonManager;
use Poppy\Framework\Routing\Abstracts\Handler;

/**
 * Class UpdateHandler.
 */
class UpdateHandler extend
s Handler
{
    /**
     * @var \System\Addon\AddonManager
     */
    protected $manager;

    /**
     * UpdateHandler constructor.
     *
     * @param \Illuminate\Container\Container       $container
     * @param \System\Addon\AddonManager $manager
     */
    public function __construct(Container $container, AddonManager $manager)
    {
        parent::__construct($container);
        $this->manager = $manager;
    }

    /**
     * Execute Handler.
     */
    public function execute()
    {
        $extension = $this->manager->get($this->request->input('name'));
        if ($extension && method_exists($provider = $extension->getEntry(), 'update') && call_user_func([
                $provider,
                'update',
            ])
        ) {
            $this->withCode(200)->withMessage('升级插件成功！');
        } else {
            $this->withCode(500)->withError('升级插件失败！');
        }
    }
}

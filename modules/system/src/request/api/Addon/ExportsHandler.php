<?php

namespace System\Addon\Handlers;

use Carbon\Carbon;
use Illuminate\Container\Container;
use System\Addon\Addon;
use System\Addon\AddonManager;
use Poppy\Framework\Router\Abstracts\Handler;
use Poppy\Framework\Validation\Rule;
use Symfony\Component\Yaml\Yaml;
use System\Setting\Repository\SettingRepository;

/**
 * Class ExportsHandler.
 */
class ExportsHandler extends Handler
{
	/**
	 * @var \System\Addon\AddonManager
	 */
	protected $extension;

	/**
	 * @var \Poppy\Framework\Setting\Contracts\SettingsRepository
	 */
	protected $setting;

	/**
	 * ExportsHandler constructor.
	 * @param \Illuminate\Container\Container                       $container
	 * @param \System\Addon\AddonManager                   $extension
	 * @param \Poppy\Framework\Setting\Contracts\SettingsRepository $setting
	 */
	public function __construct(Container $container, AddonManager $extension, SettingRepository $setting)
	{
		parent::__construct($container);
		$this->extension = $extension;
		$this->setting   = $setting;
	}

	/**
	 * Execute Handler.
	 * @throws \Exception
	 */
	protected function execute()
	{
		$this->validate($this->request, [
			'extensions' => [
				Rule::array(),
				Rule::required(),
			],
		], [
			'extensions.array'    => '插件数据必须为数组',
			'extensions.required' => '插件数据必须填写',
		]);
		$output = collect();
		collect($this->request->input('extensions'))->each(function ($identification) use ($output) {
			$extension = $this->extension->get($identification);
			$exports   = collect();
			if ($extension instanceof Addon) {
				$exports->put('name', $extension->offsetGet('name'));
				$exports->put('version', $extension->offsetGet('version'));
				$exports->put('time', Carbon::now());
				$exports->put('secret', false);
				$settings = collect($extension->get('settings', []));
				$settings->count() && $exports->put('settings', $settings->map(function ($default, $key) {
					return $this->setting->get($key, $default);
				}));
				$output->put($identification, $exports);
			}
		});
		$output = Yaml::dump($output->toArray(), 8);
		$this->withCode(200)->withData([
			'content' => $output,
			'file'    => 'Notadd extension export ' . Carbon::now()->format('Y-m-d H:i:s') . '.yaml',
		])->withMessage('导出数据成功！');
	}
}

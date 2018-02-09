<?php namespace System\Commands;


use Illuminate\Console\Command;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use System\Classes\Traits\SystemTrait;


/**
 * 项目初始化
 */
class DevHtmlCommand extends Command
{
	use SystemTrait;

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'system:dev-html';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'Develop Html generate.';


	/**
	 * Execute the console command.
	 * @throws ModuleNotFoundException
	 */
	public function handle()
	{
		$path  = poppy_path('system', 'resources/stubs/dev_html.blade.php');
		$slugs = app('poppy')->slugs()->implode('","');
		$html  = $this->getView()->file($path, [
			'site_name'    => $this->getSetting()->get('system::site.name'),
			'url'          => env('URL_SITE'),
			'modules'      => '"' . $slugs . '"',
			'translations' => json_encode($this->getTranslator()->fetch('zh'), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
		])->render();

		$path = poppy_path('system', 'resources/mixes/backend/index.html');
		$this->getFile()->put($path, $html);
		$this->info('Generate Html Success!');
	}
}

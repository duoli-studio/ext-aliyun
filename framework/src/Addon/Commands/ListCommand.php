<?php namespace Poppy\Framework\Addon\Commands;

use Illuminate\Support\Collection;
use Illuminate\Console\Command;
use Poppy\Framework\Addon\Addon;
use Poppy\Framework\Classes\Traits\PoppyTrait;

/**
 * Class ListCommand.
 */
class ListCommand extends Command
{
	use PoppyTrait;
	/**
	 * @var array
	 */
	protected $headers = [
		'Extension Name',
		'Author',
		'Description',
		'Extension Path',
		'Entry',
		'Status',
	];

	/**
	 * Configure Command.
	 */
	public function configure()
	{
		$this->setDescription('Show extension list.');
		$this->setName('extension:list');
	}

	/**
	 * Command Handler.
	 * @return bool
	 */
	public function handle()
	{
		$addons = $this->getAddon()->repository();
		$list   = new Collection();
		$this->info('Extensions list:');
		$addons->each(function (Addon $addon) use ($list) {
			$data   = collect(collect($addon->get('author'))->first());
			$author = $data->get('name');
			$data->has('email') ? $author .= ' <' . $data->get('email') . '>' : null;
			$list->push([
				$addon->identification(),
				$author,
				$addon->get('description'),
				$addon->get('directory'),
				$addon->provider(),
				'Normal',
			]);
		});
		$this->table($this->headers, $list->toArray());

		return true;
	}
}

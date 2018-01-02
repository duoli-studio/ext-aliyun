<?php

namespace System\Addon\Commands;

use Illuminate\Support\Collection;
use Illuminate\Console\Command;
use System\Addon\AddonManager;

/**
 * Class ListUnloadedCommand.
 */
class ListUnloadedCommand extends Command
{
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
		$this->setDescription('Show unloaded extension list.');
		$this->setName('extension:unloaded');
	}

	/**
	 * @param \System\Addon\AddonManager $manager
	 * @return bool
	 */
	public function handle(AddonManager $manager)
	{
		$extensions = $manager->getUnloadedExtensions();
		$list       = new Collection();
		$this->info('Unloaded extensions list:');
		$extensions->each(function (array $extension) use ($list) {
			$data   = collect($extension['authors']);
			$author = $data->get('name');
			$data->has('email') ? $author .= ' <' . $data->get('email') . '>' : null;
			$list->push([
				$extension['identification'],
				$author,
				$extension['description'],
				$extension['directory'],
				$extension['provider'],
				'Normal',
			]);
		});
		$this->table($this->headers, $list->toArray());

		return true;
	}
}

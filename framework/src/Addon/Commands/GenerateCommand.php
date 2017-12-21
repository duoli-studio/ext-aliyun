<?php namespace Poppy\Framework\Addon\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GenerateCommand.
 */
class GenerateCommand extends Command
{
	/**
	 * Configure Command.
	 */
	public function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of a extension.');
		$this->setDescription('To generate a extension from template.');
		$this->setName('extension:generate');
	}

	/**
	 * Command handler.
	 * @return bool
	 */
	public function handle()
	{
		return true;
	}
}

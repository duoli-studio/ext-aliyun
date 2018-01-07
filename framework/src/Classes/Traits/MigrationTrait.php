<?php namespace Poppy\Framework\Classes\Traits;

trait MigrationTrait
{
	/**
	 * Require (once) all migration files for the supplied module.
	 *
	 * @param string $module
	 */
	protected function requireMigrations($module)
	{
		$path = $this->getMigrationPath($module);

		$migrations = $this->laravel['files']->glob($path . '*_*.php');

		foreach ($migrations as $migration) {
			$this->laravel['files']->requireOnce($migration);
		}
	}

	/**
	 * Get migration directory path.
	 *
	 * @param string $module
	 *
	 * @return string
	 */
	protected function getMigrationPath($module)
	{
		return poppy_path($module, 'Database/Migrations');
	}
}



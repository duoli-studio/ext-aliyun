<?php namespace System\Conf\Contracts;

/**
 * Interface SettingsRepository.
 */
interface ConfContract
{

	/**
	 * Delete a setting value.
	 * @param $key
	 */
	public function delete($key);

	/**
	 * Get a setting value by key.
	 * @param      $key
	 * @param null $default
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Set a setting value from key and value.
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value);

	/**
	 * Get namespace groups and values.
	 * @param $namespace
	 * @return array
	 */
	public function getNs($namespace);
}

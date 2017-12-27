<?php namespace System\Setting\Contracts;

/**
 * Interface SettingsRepository.
 */
interface SettingContract
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
}

<?php namespace System\Pam\Auth\Provider;

use System\Models\PamAccount;

class DevelopProvider extends PamProvider
{


	/**
	 * Retrieve a user by the given credentials.
	 * DO NOT TEST PASSWORD HERE!
	 * @param  array $credentials
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 */
	public function retrieveByCredentials(array $credentials)
	{
		$credentials['type'] = PamAccount::TYPE_DEVELOP;

		return parent::retrieveByCredentials($credentials);
	}


}
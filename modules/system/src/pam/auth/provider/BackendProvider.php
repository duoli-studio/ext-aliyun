<?php namespace System\Pam\Auth\Provider;

use System\Models\PamAccount;

class BackendProvider extends PamProvider
{


	/**
	 * Retrieve a user by the given credentials.
	 * DO NOT TEST PASSWORD HERE!
	 * @param  array $credentials
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 */
	public function retrieveByCredentials(array $credentials)
	{
		$credentials['type'] = PamAccount::TYPE_BACKEND;

		return parent::retrieveByCredentials($credentials);
	}


}
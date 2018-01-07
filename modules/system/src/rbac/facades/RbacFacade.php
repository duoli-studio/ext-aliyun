<?php namespace System\Providers\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Rbac Facade
 */
class RbacFacade extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'poppy.rbac';
	}
}




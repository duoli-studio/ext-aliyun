<?php namespace System\Setting\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class SettingFacade extends IlluminateFacade
{

	protected static function getFacadeAccessor()
	{
		return 'system.setting';
	}

}
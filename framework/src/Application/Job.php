<?php namespace Poppy\Framework\Application;

use Illuminate\Bus\Queueable;

/**
 * base job in laravel
 */
abstract class Job
{

	/*
	|--------------------------------------------------------------------------
	| Queueable Jobs
	|--------------------------------------------------------------------------
	|
	| This job base class provides a central location to place any logic that
	| is shared across all of your jobs. The trait included with the class
	| provides access to the "onQueue" and "delay" queue helper methods.
	|
	*/

	use Queueable;
}


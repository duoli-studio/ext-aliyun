<?php namespace System\Events\Listeners\Exception;

use Illuminate\Validation\ValidationException;
use System\Classes\Traits\SystemTrait;

class BeforeRenderListener
{
	use SystemTrait;

	/**
	 * Create the event handler.
	 */
	public function __construct()
	{
		//
	}

	/**
	 * @param \Exception $exception
	 * @param int        $statusCode
	 * @param            $request
	 * @return array
	 */
	public function handle($exception, $statusCode, $request)
	{
		if ($exception instanceof ValidationException) {
			return [
				'message' => '验证失败',
				'errors'  => $exception->validator->errors()->getMessages(),
			];
		}
		\Log::debug($statusCode);
	}

}




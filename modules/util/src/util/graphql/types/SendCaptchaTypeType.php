<?php namespace Util\Util\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;
use Util\Models\PamCaptcha;

/**
 * Class SendCaptchaTypeType.
 */
class SendCaptchaTypeType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'send_captcha_type';
		$this->attributes['description'] = trans('util::util.graphql.send_captcha_type_desc');
		$this->attributes['values']      = [
			PamCaptcha::CON_REGISTER => [
				'description' => trans('util::util.graphql.value_register_desc'),
			],
		];
	}
}

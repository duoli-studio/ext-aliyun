<?php namespace System\Captcha\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;
use System\Models\PamCaptcha;


class CaptchaActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'CaptchaAction';
		$this->attributes['description'] = trans('system::captcha.graphql.captcha_action_type');
		$this->attributes['values']      = [
			PamCaptcha::CON_LOGIN    => [
				'description' => trans('system::captcha.graphql.captcha_action_login'),
			],
			PamCaptcha::CON_PASSWORD => [
				'description' => trans('system::captcha.graphql.captcha_action_password'),
			],
			PamCaptcha::CON_USER     => [
				'description' => trans('system::captcha.graphql.captcha_action_user'),
			],
			PamCaptcha::CON_REBIND   => [
				'description' => trans('system::captcha.graphql.captcha_action_rebind'),
			],
		];
	}
}

<?php namespace User\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class CaptchaRegisterType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'captcha_register';
		$this->attributes['description'] = trans('user::captcha_register.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('user::captcha_register.db.id'),
			],
			'mobile'      => [
				'type'        => Type::string(),
				'description' => trans('user::captcha_register.db.mobile'),
			],
			'password'     => [
				'type'        => Type::string(),
				'description' => trans('user::captcha_register.db.password'),

			],
		];
	}
}

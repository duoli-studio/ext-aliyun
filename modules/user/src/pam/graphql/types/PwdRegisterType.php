<?php namespace User\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class PwdRegisterType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'pwd_register';
		$this->attributes['description'] = trans('user::pwd_register.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('user::pwd_register.db.id'),
			],
			'mobile'      => [
				'type'        => Type::string(),
				'description' => trans('user::pwd_register.db.mobile'),
			],
			'password'     => [
				'type'        => Type::string(),
				'description' => trans('user::pwd_register.db.password'),

			],
		];
	}
}

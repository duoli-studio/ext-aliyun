<?php namespace User\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class UpdatePasswordType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'update_password';
		$this->attributes['description'] = trans('user::update_password.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('user::update_password.db.id'),
			],
			'mobile'      => [
				'type'        => Type::string(),
				'description' => trans('user::update_password.db.mobile'),
			],
			'password'     => [
				'type'        => Type::string(),
				'description' => trans('user::update_password.db.password'),

			],
		];
	}
}

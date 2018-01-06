<?php namespace System\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class BindChangeType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'bind_change';
		$this->attributes['description'] = trans('system::bind_change.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'mobile' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::bind_change.db.mobile'),
			],
			'captcha' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::bind_change.db.captcha'),
			],
		];
	}
}

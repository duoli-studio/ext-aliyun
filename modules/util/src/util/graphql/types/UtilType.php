<?php namespace Util\Util\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class UtilType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'util';
		$this->attributes['description'] = trans('util::act.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'       => [
				'type'        => Type::int(),
				'description' => trans('util::act.db.id'),
			],
			'passport' => [
				'type'        => Type::string(),
				'description' => trans('util::act.db.passport'),
			],
			'captcha'  => [
				'type'        => Type::string(),
				'description' => trans('util::act.db.captcha'),
			],
			'type'     => [
				'type'        => Type::string(),
				'description' => trans('util::act.db.type'),
			],
			'num'      => [
				'type'        => Type::int(),
				'description' => trans('util::act.db.num'),
			],
		];
	}
}

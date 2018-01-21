<?php namespace System\Help\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;

/**
 * Class SettingType.
 */
class InputHelpType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'InputHelp';
		$this->attributes['description'] = trans('system::help.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::help.db.type'),
			],
			'cat_id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::help.db.cat_id'),
			],
			'title'   => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::help.db.title'),
			],
			'content'    => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::help.db.content'),
			],
		];
	}
}

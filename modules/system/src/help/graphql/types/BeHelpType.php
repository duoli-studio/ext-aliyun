<?php namespace System\Help\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;


class BeHelpType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeHelp';
		$this->attributes['description'] = trans('system::help.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'         => [
				'type'        => Type::int(),
				'description' => trans('system::help.db.id'),
			],
			'type'      => [
				'type'        => Type::string(),
				'description' => trans('system::help.db.type'),

			],
			'cat_id'       => [
				'type'        => Type::int(),
				'description' => trans('system::help.db.cat_id'),

			],
			'cat_title'       => [
				'type'        => Type::string(),
				'description' => trans('system::category.db.title'),

			],
			'title'      => [
				'type'        => Type::string(),
				'description' => trans('system::help.db.title'),

			],
			'content'  => [
				'type'        => Type::string(),
				'description' => trans('system::help.db.content'),
			],
		];
	}
}

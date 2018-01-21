<?php namespace System\Pam\Graphql\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;


class BePamFilterType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BePamFilter';
		$this->attributes['description'] = trans('system::account.graphql.input_pam_filter');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'type' => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.type'),
			],
			'id'   => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.id'),
			],
		];
	}
}

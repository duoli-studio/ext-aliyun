<?php namespace System\Help\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

class BeCategoryDoActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeCategoryDoAction';
		$this->attributes['description'] = trans('system::category.graphql.type_desc');
		$this->attributes['values']      = [
			'delete'      => [
				'description' => trans('system::help.action.delete'),
			],
		];
	}
}

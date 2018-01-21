<?php namespace System\Area\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

class BeAreaDoActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeAreaDoAction';
		$this->attributes['description'] = trans('system::area.graphql.action_type');
		$this->attributes['values']      = [
			'delete'      => [
				'description' => trans('system::area.action.delete'),
			],
		];
	}
}

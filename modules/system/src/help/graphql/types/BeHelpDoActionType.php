<?php namespace System\Help\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

class BeHelpDoActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeHelpDoAction';
		$this->attributes['description'] = trans('system::help.graphql.type_desc');
		$this->attributes['values']      = [
			'delete'      => [
				'description' => trans('system::help.action.delete'),
			],
		];
	}
}

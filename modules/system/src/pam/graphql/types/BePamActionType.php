<?php namespace System\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

class BePamActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BePamAction';
		$this->attributes['description'] = trans('system::account.graphql.action_desc');
		$this->attributes['values']      = [
			'enable'  => [
				'description' => trans('system::account.action.enable'),
			],
		];
	}
}

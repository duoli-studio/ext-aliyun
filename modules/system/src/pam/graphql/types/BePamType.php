<?php namespace System\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

class BePamType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BePam';
		$this->attributes['description'] = trans('system::account.graphql.list_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'             => [
				'type'        => Type::int(),
				'description' => trans('system::account.db.id'),
			],
			'username'       => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.username'),
			],
			'mobile'         => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.mobile'),
			],
			'email'          => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.email'),
			],
			'type'           => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.type'),
			],
			'is_enable'      => [
				'type'        => Type::int(),
				'description' => trans('system::account.db.is_enable'),
			],
			'disable_reason' => [
				'type'        => Type::string(),
				'description' => trans('system::account.db.disable_reason'),
			],
		];
	}
}
